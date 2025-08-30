package co.edu.grocerystore.fragments;

import static android.util.Base64.NO_WRAP;

import static co.edu.grocerystore.api.ValuesApi.BASE_URL;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;

import androidx.appcompat.app.AlertDialog;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;

import android.util.Base64;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.Collection;

import co.edu.grocerystore.R;
import co.edu.grocerystore.api.ServiceUsers;
import co.edu.grocerystore.login;
import co.edu.grocerystore.model.ResponseCredentials;
import co.edu.grocerystore.model.ResponseUser;
import co.edu.grocerystore.model.User;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

/**
 * A simple {@link Fragment} subclass.
 * Use the {@link profile#newInstance} factory method to
 * create an instance of this fragment.
 */
public class profile extends Fragment {
    private Retrofit retrofit;
    private TextView tvUserName;
    private TextView tvUserEmail;
    private Button btnEdit;
    private Button btnLogout;
    private Button btnDeleteAccount;
    private ImageView returnImg;
    private User currentUser = new User();
    public profile() {
        // Required empty public constructor
    }

    public static profile newInstance() {
        profile fragment = new profile();
        Bundle args = new Bundle();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    public static boolean isNullOrEmpty(Object obj){
        if(obj==null)return true;
        if(obj instanceof String) return ((String)obj).trim().equals("") || ((String)obj).equalsIgnoreCase("NULL");
        if(obj instanceof Integer) return ((Integer)obj)==0;
        if(obj instanceof Long) return ((Long)obj).equals(new Long(0));
        if(obj instanceof Double) return ((Double)obj).equals(0.0);
        if(obj instanceof Collection) return (((Collection)obj).isEmpty());
        return false;
    }

    private void getUserById(String auth, String id, View view){
        retrofit = ClientRetroFit.getClient(BASE_URL);
        ServiceUsers serviceUsers = retrofit.create(ServiceUsers.class);
        Call<ResponseUser> call = serviceUsers.getUser(id, auth);
        call.enqueue(new Callback<ResponseUser>() {
            @Override
            public void onResponse(Call<ResponseUser> call, Response<ResponseUser> response) {
                ResponseUser body = response.body();
                ArrayList<User> list = body.getUser();
                if(!isNullOrEmpty(list)){
                    currentUser = list.get(0);
                    tvUserEmail.setText(currentUser.getUser_email());
                    tvUserName.setText(currentUser.getUser_name() + " " +currentUser.getUser_lastname());
                } else {
                    Toast.makeText(getActivity(), "No error", Toast.LENGTH_SHORT).show();
                    Log.i("ERROR1", "onResponse: "+response.body());
                }
            }

            @Override
            public void onFailure(Call<ResponseUser> call, Throwable t) {
                Toast.makeText(getActivity(), "error:"+t.getMessage(), Toast.LENGTH_SHORT).show();
                Log.i("ERROR2", "onFailure: "+ t.getMessage());
            }
        });
    }

    private void deleteUser(String auth, String id, View view){
        retrofit = ClientRetroFit.getClient(BASE_URL);
        ServiceUsers serviceUsers = retrofit.create(ServiceUsers.class);
        Call<ResponseCredentials> call = serviceUsers.deleteUser(id, auth);
        call.enqueue(new Callback<ResponseCredentials>() {
            @Override
            public void onResponse(Call<ResponseCredentials> call, Response<ResponseCredentials> response) {
                ResponseCredentials body = response.body();
                String message = body.getMessage();
                if(response.isSuccessful()){
                    Toast.makeText(getActivity(), "Adios, " + currentUser.getUser_name(), Toast.LENGTH_SHORT).show();
                    logout(view);
                } else {
                    Toast.makeText(getActivity(), "No se pudo eliminar la cuenta", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<ResponseCredentials> call, Throwable t) {
                Toast.makeText(getActivity(), "Error desconocido", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void alertView(View view){
        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        builder.setTitle("Eliminar cuenta");
        builder.setMessage(currentUser.getUser_name() + ", ¿deseas eliminar tu cuenta?, esta acción es irreversible.");
        builder.setPositiveButton("Eliminar", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                deleteUser(getAuth(),getUserId(),view);
            }
        });
        builder.setNegativeButton("Cancelar",null);
        builder.create();
        builder.show();
    }


    private String getAuth(){
        SharedPreferences preferences = getActivity().getSharedPreferences("credenciales", Context.MODE_PRIVATE);
        String key = preferences.getString("key","No");
        String identifier = preferences.getString("identifier","No");
        String token = identifier + ":" + key;
        final String AUTH = "Basic " + Base64.encodeToString((token).getBytes(), NO_WRAP);
        return AUTH;
    }

    private String getUserId(){
        SharedPreferences preferences = getActivity().getSharedPreferences("credenciales", Context.MODE_PRIVATE);
        String id = preferences.getString("id","NoID");
        return id;
    }

    public void loadFragment(Fragment fragment){
        FragmentManager fragmentManager = getActivity().getSupportFragmentManager();
        FragmentTransaction transaction = fragmentManager.beginTransaction();
        transaction.replace(R.id.frame_container,fragment).addToBackStack(null).commit();
    }

    private void editUser(View view){
        Fragment editUser = new editProfile();
        Bundle bundle = new Bundle();
        bundle.putSerializable("currentUser",currentUser);
        editUser.setArguments(bundle);
        loadFragment(editUser);
    }

    private void logout(View view){
        SharedPreferences preferences = getActivity().getSharedPreferences("credenciales", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = preferences.edit();
        editor.clear();
        editor.commit();
        Intent home = new Intent(getActivity(), login.class);
        startActivity(home);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_profile, container, false);
        begin(view);
        getUserById(getAuth(), getUserId(), view);
        btnEdit.setOnClickListener(this::editUser);
        btnLogout.setOnClickListener(this::logout);
        btnDeleteAccount.setOnClickListener(this::alertView);
        return view;
    }

    public void begin(View view){
        this.tvUserName = view.findViewById(R.id.tvName);
        this.tvUserEmail = view.findViewById(R.id.tvEmail);
        this.btnEdit = view.findViewById(R.id.btnEdit);
        this.btnLogout = view.findViewById(R.id.btnLogout);
        this.btnDeleteAccount = view.findViewById(R.id.btnDeleteAccount);
    }
}