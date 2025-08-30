package co.edu.grocerystore;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

public class home extends AppCompatActivity {

    private TextView tvWelcome;
    private Button btnWelcome;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_home);
        begin();
        this.btnWelcome.setOnClickListener(this::store);
    }

    private void store(View view) {
        Intent goStore = new Intent(getApplicationContext(), store.class);
        startActivity(goStore);
    }

    private void begin(){
        this.tvWelcome = findViewById(R.id.tvWelcome);
        this.btnWelcome = findViewById(R.id.btnContinueHome);
    }
}