package co.edu.grocerystore.fragments;

import static android.util.Base64.NO_WRAP;

import static co.edu.grocerystore.api.ValuesApi.BASE_URL;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.Bundle;

import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;

import android.util.Base64;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.Toast;

import com.google.android.material.snackbar.Snackbar;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import co.edu.grocerystore.R;
import co.edu.grocerystore.api.ServiceUsers;
import co.edu.grocerystore.model.ResponseCredentials;
import co.edu.grocerystore.model.User;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

/**
 * A simple {@link Fragment} subclass.
 * Use the {@link editProfile#newInstance} factory method to
 * create an instance of this fragment.
 */
public class editProfile extends Fragment {
    private EditText etName;
    private EditText etLastName;
    private Spinner etDocType;
    private EditText etDocNumber;
    private EditText etEmail;
    private EditText etPhone;
    private EditText etAddress;
    private Button btnUpdate;
    private Button btnSaveUser;
    private Button btnCancel;
    private ImageView btnReturn;
    private User currentUser;
    private String name;
    private String lastname;
    private String doctype;
    private String docnumber;
    private String address;
    private String email;
    private String phone;
    private Retrofit retrofit;

    public editProfile() {
        // Required empty public constructor
    }

    // TODO: Rename and change types and number of parameters
    public static editProfile newInstance() {
        editProfile fragment = new editProfile();
        Bundle args = new Bundle();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_edit_profile, container, false);
        Bundle bundle = this.getArguments();
        if(bundle != null){
            currentUser = (User) bundle.getSerializable("currentUser");
        }
        begin(view);
        initForm();
        btnUpdate.setOnClickListener(this::editableForm);
        btnSaveUser.setOnClickListener(this::getCredentials);
        btnCancel.setOnClickListener(this::cancelUpdate);
        btnReturn.setOnClickListener(this::profile);
        return view;
    }

    public void begin(View view){
        this.etName = view.findViewById(R.id.etPutName);
        this.etLastName = view.findViewById(R.id.etPutLastname);
        this.etDocType = view.findViewById(R.id.spPutDocumentType);
        this.etDocNumber = view.findViewById(R.id.etPutDocnumber);
        this.etEmail = view.findViewById(R.id.etPutEmail);
        this.etPhone = view.findViewById(R.id.etPutPhone);
        this.etAddress = view.findViewById(R.id.etPutAddress);
        this.btnUpdate = view.findViewById(R.id.btnUpdate);
        this.btnSaveUser = view.findViewById(R.id.btnSave);
        this.btnCancel = view.findViewById(R.id.btnCancelSave);
        this.btnReturn = view.findViewById(R.id.profileReturn);
    }

    public void initForm(){
        setUserData();
        etName.setFocusable(false);
        etLastName.setFocusable(false);
        etDocType.setEnabled(false);
        etDocNumber.setFocusable(false);
        etEmail.setFocusable(false);
        etPhone.setFocusable(false);
        etAddress.setFocusable(false);
        btnUpdate.setVisibility(View.VISIBLE);
        btnSaveUser.setVisibility(View.GONE);
        btnCancel.setVisibility(View.GONE);
    }

    public void setUserData(){
        etName.setText(currentUser.getUser_name());
        etLastName.setText(currentUser.getUser_lastname());
        if(currentUser.getUser_doctype() == "Cédula de ciudadanía"){
            etDocType.setSelection(0);
        } else {
            etDocType.setSelection(1);
        }
        etDocNumber.setText(currentUser.getUser_docnumber());
        etEmail.setText(currentUser.getUser_email());
        etPhone.setText(currentUser.getUser_phone());
        etAddress.setText(currentUser.getUser_address());
    }

    public void cancelUpdate(View view){
        setUserData();
        initForm();
    }

    public void editableForm(View view){
        etName.setFocusable(true);
        etLastName.setFocusable(true);
        etDocType.setEnabled(true);
        etDocNumber.setFocusable(true);
        etEmail.setFocusable(true);
        etPhone.setFocusable(true);
        etAddress.setFocusable(true);
        btnUpdate.setVisibility(View.GONE);
        btnSaveUser.setVisibility(View.VISIBLE);
        btnCancel.setVisibility(View.VISIBLE);
    }

    private void updateUser(String identifier, String key, String id, View view){
        setFormData();
        if(validations()){
            String token = identifier + ":" + key;
            final String AUTH = "Basic " + Base64.encodeToString((token).getBytes(), NO_WRAP);
            currentUser.setUser_name(name);
            currentUser.setUser_lastname(lastname);
            currentUser.setUser_phone(phone);
            currentUser.setUser_doctype(doctype);
            currentUser.setUser_docnumber(docnumber);
            currentUser.setUser_email(email);
            currentUser.setUser_address(address);
            retrofit = ClientRetroFit.getClient(BASE_URL);
            ServiceUsers serviceUsers = retrofit.create(ServiceUsers.class);
            Call<ResponseCredentials> call = serviceUsers.updateUser(id, AUTH, currentUser);
            call.enqueue(new Callback<ResponseCredentials>() {
                @Override
                public void onResponse(Call<ResponseCredentials> call, Response<ResponseCredentials> response) {
                    ResponseCredentials body = response.body();
                    String message = body.getMessage();
                    if(message.equals("ok")){
                        Toast.makeText(getActivity(), "Usuario actualizado", Toast.LENGTH_SHORT).show();
                        profile(view);
                    } else {
                        Toast.makeText(getActivity(), "Este correo ya esta registrado", Toast.LENGTH_SHORT).show();
                    }

                }

                @Override
                public void onFailure(Call<ResponseCredentials> call, Throwable t) {
                    Toast.makeText(getActivity(), "Error desconocido", Toast.LENGTH_SHORT).show();
                }
            });
        }
    }

    public void loadFragment(Fragment fragment){
        FragmentManager fragmentManager = getActivity().getSupportFragmentManager();
        FragmentTransaction transaction = fragmentManager.beginTransaction();
        transaction.replace(R.id.frame_container,fragment).addToBackStack(null).commit();
    }

    private void getCredentials(View view){
        SharedPreferences preferences = getActivity().getSharedPreferences("credenciales", Context.MODE_PRIVATE);
        String key = preferences.getString("key","No");
        String identificador = preferences.getString("identifier","No");
        String id = preferences.getString("id","NoID");
        updateUser(identificador, key, id, view);
    }


    private void setFormData(){
        name = etName.getText().toString();
        lastname = etLastName.getText().toString();
        doctype = etDocType.getSelectedItem().toString();
        docnumber = etDocNumber.getText().toString();
        email = etEmail.getText().toString();
        phone = etPhone.getText().toString();
        address = etAddress.getText().toString();
    }

    private void profile(View view){
        Fragment profile = new profile();
        loadFragment(profile);
    }

    private boolean validations(){
        boolean formValid = false;
        if(!validateNameLastName(name)){
            if(name.isEmpty()){
                Toast.makeText(getActivity(), "Por favor ingrese su nombre.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(getActivity(), "El campo nombre no puede tener valores númericos ni algunos carácteres especiales.", Toast.LENGTH_SHORT).show();
            }
        } else if(!validateNameLastName(lastname)){
            if(lastname.isEmpty()){
                Toast.makeText(getActivity(), "Por favor ingrese su apellido.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(getActivity(), "El campo apellido no puede tener valores númericos ni algunos carácteres especiales.", Toast.LENGTH_SHORT).show();
            }
        } else if (!validateEmail(email)) {
            if(email.isEmpty()){
                Toast.makeText(getActivity(), "Por favor ingrese su correo.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(getActivity(), "Por favor ingrese un correo válido.", Toast.LENGTH_SHORT).show();
            }
        } else if (!validateDocNumber(docnumber)) {
            if(docnumber.isEmpty()){
                Toast.makeText(getActivity(), "Por favor ingrese su número de documento.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(getActivity(), "El campo número de documento debe contener como mínimo 5 dígitos númericos y máximo 12.", Toast.LENGTH_SHORT).show();
            }
        } else if(address.isEmpty()) {
            Toast.makeText(getActivity(), "Por favor ingrese su dirección.", Toast.LENGTH_SHORT).show();
        } else if (!validatePhoneNumber(phone)) {
            if(phone.isEmpty()){
                Toast.makeText(getActivity(), "Por favor ingrese su télefono.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(getActivity(), "El campo télefono debe contener 10 digitos númericos.", Toast.LENGTH_SHORT).show();
            }
        } else {
            formValid = true;
        }
        return formValid;
    }

    private boolean validateNameLastName(String input){
        Pattern regex = Pattern.compile("^[a-zA-Z ÁáüÜñÑúÚ]+$");
        Matcher matchs = regex.matcher(input);
        return matchs.matches();
    }

    private boolean validateEmail(String input){
        Pattern regex = Pattern.compile("^((?!\\.)[\\w-_.]*[^.])(@\\w+)(\\.\\w+(\\.\\w+)?[^.\\W])$");
        Matcher matchs = regex.matcher(input);
        return matchs.matches();
    }

    private boolean validateDocNumber(String input){
        Pattern regex = Pattern.compile("^[0-9]{5,12}$");
        Matcher matchs = regex.matcher(input);
        return matchs.matches();
    }

    private boolean validatePhoneNumber(String input){
        Pattern regex = Pattern.compile("^[0-9]{10}$");
        Matcher matchs = regex.matcher(input);
        return matchs.matches();
    }
}