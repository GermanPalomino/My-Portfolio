package co.edu.grocerystore;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;

public class MainActivity extends AppCompatActivity {

    private Button btnLogin;
    private Button btnRegister;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        begin();
        this.btnLogin.setOnClickListener(this::login);
        this.btnRegister.setOnClickListener(this::register);
    }

    private void login(View view) {
        Intent goLogin = new Intent(getApplicationContext(), login.class);
        startActivity(goLogin);
    }

    private void register(View view) {
        Intent goRegister = new Intent(getApplicationContext(), register.class);
        startActivity(goRegister);
    }

    public void begin(){
        this.btnLogin = findViewById(R.id.btnHomeLogin);
        this.btnRegister = findViewById(R.id.btnHomeRegister);
    }
}