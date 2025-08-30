package co.edu.grocerystore;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class forgotpassword extends AppCompatActivity {

    private Button btnReturn;
    private Button btnSend;
    private TextView tvSent;
    private EditText etEmail;
    private String email;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_forgot_password);
        begin();
        this.btnSend.setOnClickListener(this::send);
        this.btnReturn.setOnClickListener(this::main);
    }

    private void send(View view) {
        data();
        if(validateEmail(email)){
            this.tvSent.setText("¡El correo fue enviado!");
        } else if(email.isEmpty()){
            Toast.makeText(this, "Por favor ingrese su correo.", Toast.LENGTH_SHORT).show();
        } else {
            Toast.makeText(this, "Ingrese un correo válido por favor.", Toast.LENGTH_SHORT).show();
        }
    }

    private void main(View view) {
        Intent goMain = new Intent(getApplicationContext(), MainActivity.class);
        startActivity(goMain);
    }

    private boolean validateEmail(String input){
        Pattern regex = Pattern.compile("^((?!\\.)[\\w-_.]*[^.])(@\\w+)(\\.\\w+(\\.\\w+)?[^.\\W])$");
        Matcher matchs = regex.matcher(input);
        return matchs.matches();
    }

    private void begin(){
        this.btnSend = findViewById(R.id.btnSend);
        this.btnReturn = findViewById(R.id.btnReturnForgot);
        this.tvSent = findViewById(R.id.tvSent);
        this.etEmail = findViewById(R.id.etEmailForgot);
    }
    private void data(){
        this.email = this.etEmail.getText().toString();
    }
}