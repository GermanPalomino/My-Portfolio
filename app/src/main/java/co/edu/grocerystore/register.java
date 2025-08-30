package co.edu.grocerystore;

import static co.edu.grocerystore.api.ValuesApi.BASE_URL;

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import co.edu.grocerystore.api.ServiceUsers;
import co.edu.grocerystore.model.Register;
import co.edu.grocerystore.model.ResponseCredentials;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class register extends AppCompatActivity {

    private EditText etNames;
    private EditText etLastNames;
    private Spinner spDocumentType;
    private EditText etDocumentNumber;
    private EditText etEmail;
    private EditText etPhone;
    private EditText etPassword;
    private EditText etConfirmPassword;
    private EditText etAddress;
    private Button btnRegistro;
    private Button btnReturn;
    private TextView tvLogin;
    private String name;
    private String lastname;
    private String doctype;
    private String docnumber;
    private String address;
    private String email;
    private String phone;
    private String password;
    private String confirmpass;
    private Retrofit retrofit;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);
        begin();
        btnRegistro.setOnClickListener(this::processRegister);
        tvLogin.setOnClickListener(this::login);
        btnReturn.setOnClickListener(this::main);
    }

    private void alertView(String msj){
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Login");
        builder.setMessage(msj);
        builder.setPositiveButton("Aceptar", null);
        builder.create();
        builder.show();
    }

    private void main(View view) {
        Intent goMain = new Intent(getApplicationContext(), MainActivity.class);
        startActivity(goMain);
    }

    private void login(View view) {
        Intent goLogin = new Intent(getApplicationContext(), login.class);
        startActivity(goLogin);
    }

    private void processRegister(View view) {
        data();
        if(validations()){
            Register register = new Register();
            register.setUser_name(name);
            register.setUser_lastname(lastname);
            register.setUser_phone(phone);
            register.setUser_doctype(doctype);
            register.setUser_docnumber(docnumber);
            register.setUser_email(email);
            register.setUser_address(address);
            register.setUser_password(password);
            retrofit = ClientRetroFit.getClient(BASE_URL);
            ServiceUsers serviceUsers = retrofit.create(ServiceUsers.class);
            Call<ResponseCredentials> call = serviceUsers.accesRegister(register);
            call.enqueue(new Callback<ResponseCredentials>() {
                @Override
                public void onResponse(Call<ResponseCredentials> call, Response<ResponseCredentials> response) {
                    if(response.isSuccessful()){
                        ResponseCredentials body = response.body();
                        String mensaje = body.getMessage();
                        if(mensaje.equals("ok")){
                            goLogin();
                        } else {
                            alertView("Error durante el registro, Intentelo de nuevo");
                        }
                    } else {
                        alertView("Error durante el registro, Intentelo de nuevo");
                    }
                }

                @Override
                public void onFailure(Call<ResponseCredentials> call, Throwable t) {
                    Log.i("Reponse2",t.getMessage());
                    alertView("Error en Servicio comuniquese con el Programador");
                }
            });
        }
    }

    private void goLogin(){
        try{
            Intent goLogin = new Intent(getApplicationContext(), login.class);
            startActivity(goLogin);
        }catch(Exception e){

        }
    }

    private boolean validations(){
        boolean formValid = false;
        if(!validateNameLastName(name)){
            if(name.isEmpty()){
                Toast.makeText(this, "Por favor ingrese su nombre.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(this, "El campo nombre no puede tener valores númericos ni algunos carácteres especiales.", Toast.LENGTH_SHORT).show();
            }
        } else if(!validateNameLastName(lastname)){
            if(lastname.isEmpty()){
                Toast.makeText(this, "Por favor ingrese su apellido.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(this, "El campo apellido no puede tener valores númericos ni algunos carácteres especiales.", Toast.LENGTH_SHORT).show();
            }
        } else if (!validateEmail(email)) {
            if(email.isEmpty()){
                Toast.makeText(this, "Por favor ingrese su correo.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(this, "Por favor ingrese un correo válido.", Toast.LENGTH_SHORT).show();
            }
        } else if (!validateDocNumber(docnumber)) {
            if(docnumber.isEmpty()){
                Toast.makeText(this, "Por favor ingrese su número de documento.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(this, "El campo número de documento debe contener como mínimo 5 dígitos númericos y máximo 12.", Toast.LENGTH_SHORT).show();
            }
        } else if(address.isEmpty()) {
            Toast.makeText(this, "Por favor ingrese su dirección.", Toast.LENGTH_SHORT).show();
        } else if (!validatePhoneNumber(phone)) {
            if(phone.isEmpty()){
                Toast.makeText(this, "Por favor ingrese su télefono.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(this, "El campo télefono debe contener 10 digitos númericos.", Toast.LENGTH_SHORT).show();
            }
        } else if (!validatePassword(password, confirmpass)){
            if(password.isEmpty()){
                Toast.makeText(this, "Por favor ingrese su contraseña.", Toast.LENGTH_SHORT).show();
            } else if(confirmpass.isEmpty()){
                Toast.makeText(this, "Por favor confirme su contraseña.", Toast.LENGTH_SHORT).show();
            } else if (password != confirmpass){
                Toast.makeText(this, "Las contraseñas no coinciden", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(this, "La contraseña solo puede contener minúsculas, mayúsculas y números", Toast.LENGTH_SHORT).show();
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

    private boolean validatePassword(String password1, String password2){
        Pattern regex = Pattern.compile("^[a-zA-Z0-9]+$");
        Matcher matchs = regex.matcher(password1);
        if(matchs.matches() && password2.equals(password1)){
            return true;
        } else {
            return false;
        }
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

    public void begin(){
        this.etNames = findViewById(R.id.etNames);
        this.etLastNames = findViewById(R.id.etLastNames);
        this.spDocumentType = findViewById(R.id.spDocumentType);
        this.etDocumentNumber = findViewById(R.id.etDocumentNumber);
        this.etAddress = findViewById(R.id.etAddress);
        this.etEmail = findViewById(R.id.etEmail);
        this.etPhone = findViewById(R.id.etPhone);
        this.etPassword = findViewById(R.id.etPassword);
        this.etConfirmPassword = findViewById(R.id.etConfirmPassword);
        this.btnRegistro = findViewById(R.id.btnRegister);
        this.tvLogin = findViewById(R.id.tvLogin);
        this.btnReturn = findViewById(R.id.btnReturnRegister);
    }

    public void data(){
        this.name = etNames.getText().toString();
        this.lastname = etLastNames.getText().toString();
        this.doctype = spDocumentType.getSelectedItem().toString();
        this.docnumber = etDocumentNumber.getText().toString();
        this.address = etAddress.getText().toString();
        this.email = etEmail.getText().toString();
        this.phone = etPhone.getText().toString();
        this.password = etPassword.getText().toString();
        this.confirmpass = etConfirmPassword.getText().toString();
    }
}