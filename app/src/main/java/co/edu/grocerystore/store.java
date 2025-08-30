package co.edu.grocerystore;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;

import android.content.Intent;
import android.os.Bundle;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;

import com.google.android.material.bottomnavigation.BottomNavigationView;
import com.google.android.material.navigation.NavigationBarView;

import co.edu.grocerystore.fragments.catalog;
import co.edu.grocerystore.fragments.confirmTransaction;
import co.edu.grocerystore.fragments.editProfile;
import co.edu.grocerystore.fragments.productView;
import co.edu.grocerystore.fragments.profile;
import co.edu.grocerystore.fragments.shopCar;

public class store extends AppCompatActivity {
    catalog catalogFragment = new catalog();
    profile profileFragment = new profile();
    shopCar shopCarFragment = new shopCar();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_store);
        BottomNavigationView navigationView = findViewById(R.id.bottom_navigation);
        selectedFragment(navigationView);
        loadFragment(catalogFragment);
    }

    private void loadFragment(Fragment fragment){
        FragmentTransaction transaction = getSupportFragmentManager().beginTransaction();
        transaction.replace(R.id.frame_container,fragment);
        transaction.commit();
    }

    private void selectedFragment(BottomNavigationView navigationView){
        navigationView.setOnItemSelectedListener(new NavigationBarView.OnItemSelectedListener() {
            @Override
            public boolean onNavigationItemSelected(@NonNull MenuItem item) {
                int id = item.getItemId();
                if(id == R.id.homeFragment){
                    loadFragment(catalogFragment);
                    return true;
                } else if(id == R.id.profileFragment) {
                    loadFragment(profileFragment);
                    return true;
                } else if (id == R.id.carFragment) {
                    loadFragment(shopCarFragment);
                    return  true;
                }
                return false;
            }
        });
    }
}