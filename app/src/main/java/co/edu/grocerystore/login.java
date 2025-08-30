package co.edu.grocerystore;

import static co.edu.grocerystore.api.ValuesApi.BASE_URL;

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import co.edu.grocerystore.api.ServiceLogin;
import co.edu.grocerystore.model.Credentials;
import co.edu.grocerystore.model.Loger;
import co.edu.grocerystore.model.ResponseCredentials;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class login extends AppCompatActivity {
    private Retrofit retrofit;
    private EditText etEmail;
    private EditText etPassword;
    private TextView tvForgotPassword;
    private TextView tvLoginRegister;
    private Button btnLogin;
    private Button btnReturn;
    private String email;
    private String password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        begin();
        tvForgotPassword.setOnClickListener(this::forgotPassword);
        tvLoginRegister.setOnClickListener(this::register);
        btnLogin.setOnClickListener(this::processLogin);
        btnReturn.setOnClickListener(this::main);
    }

    private void main(View view) {
        Intent goMain = new Intent(getApplicationContext(), MainActivity.class);
        startActivity(goMain);
    }

    private void forgotPassword(View view) {
        Intent goForgotPassword = new Intent(getApplicationContext(), forgotpassword.class);
        startActivity(goForgotPassword);
    }

    private void register(View view) {
        Intent goRegister = new Intent(getApplicationContext(), register.class);
        startActivity(goRegister);
    }

    private void processLogin(View view) {
        data();
        if (!validEmail(email) && password.isEmpty()) {
            alertView("Error en los datos");
        } else {
            Loger loger = new Loger();
            loger.setUser_email(email);
            loger.setUser_password(password);
            retrofit = ClientRetroFit.getClient(BASE_URL);
            ServiceLogin serviceLogin = retrofit.create(ServiceLogin.class);
            Call<ResponseCredentials> call = serviceLogin.accesLogin(loger);
            call.enqueue(new Callback<ResponseCredentials>() {
                @Override
                public void onResponse(Call<ResponseCredentials> call, Response<ResponseCredentials> response) {
                    if (response.isSuccessful()) {
                        ResponseCredentials body = response.body();
                        String mensaje = body.getMessage();
                        ArrayList<Credentials> list = body.getCredentials();

                        if (mensaje.equals(("OK")) && !isNullOrEmpty(list)) {
                            Toast.makeText(login.this, "Ingresado exitosamente", Toast.LENGTH_SHORT).show();
                            for (Credentials c : list) {
                                SharedPreferences shared = getSharedPreferences("credenciales", Context.MODE_PRIVATE);
                                SharedPreferences.Editor editor = shared.edit();
                                editor.putString("key", c.getUser_key());
                                editor.putString("identifier", c.getUser_identifier());
                                editor.putString("id", c.getUser_id());
                                editor.commit();
                            }
                            store();
                        } else {
                            alertView("Usuario no existe o Contraseña Inválida!, Intentelo de nuevo");
                        }
                    } else {
                        alertView("Usuario no existe, intente de nuevo");
                    }
                }

                @Override
                public void onFailure(Call<ResponseCredentials> call, Throwable t) {
                    Log.i("Reponse2", t.getMessage());
                    alertView("Error en Servicio comuniquese con el Programador");
                }
            });
        }
    }

    private void store() {
        try {
            Intent goStore = new Intent(getApplicationContext(), store.class);
            startActivity(goStore);
            finish();
        } catch (Exception e){

        }
    }

    private void alertView(String msj){
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Iniciar sesión");
        builder.setMessage(msj);
        builder.setPositiveButton("Aceptar", null);
        builder.create();
        builder.show();
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

    public boolean validEmail(String data){
        Pattern pattern =
                Pattern.compile("^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~\\-]+@[a-zA-Z0-9-]+(?:\\.[a-zA-Z0-9-]+)*$");
        Matcher mather = pattern.matcher(data);
        if (mather.find() == true) {
            System.out.println("El email ingresado es válido.");
            return true;
        } else {
            System.out.println("El email ingresado es inválido.");
        }
        return false;
    }

    public static String md5(final String s) {
        final String MD5 = "MD5";
        try {
            // Create MD5 Hash
            MessageDigest digest = MessageDigest.getInstance(MD5);
            digest.update(s.getBytes());
            byte messageDigest[] = digest.digest();
            // Create Hex String
            StringBuilder hexString = new StringBuilder();
            for (byte aMessageDigest : messageDigest) {
                String h = Integer.toHexString(0xFF & aMessageDigest);
                while (h.length() < 2)
                    h = "0" + h;
                hexString.append(h);
            }
            return hexString.toString();
        } catch (NoSuchAlgorithmException e) {
            e.printStackTrace();
        }
        return "";
    }

    public void begin() {
        this.etEmail = findViewById(R.id.etLoginEmail);
        this.etPassword = findViewById(R.id.etLoginPassword);
        this.tvForgotPassword = findViewById(R.id.tvForgotPassword);
        this.tvLoginRegister = findViewById(R.id.tvLoginRegister);
        this.btnLogin = findViewById(R.id.btnLogin);
        this.btnReturn = findViewById(R.id.btnReturnLogin);
    }

    private void data(){
        email = etEmail.getText().toString();
        password = etPassword.getText().toString();
    }
}