package co.edu.grocerystore;

import static android.util.Base64.NO_WRAP;
import static co.edu.grocerystore.api.ValuesApi.BASE_URL;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Base64;
import android.util.Log;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.Collection;

import co.edu.grocerystore.adapters.billAdapter;
import co.edu.grocerystore.adapters.carAdapter;
import co.edu.grocerystore.api.ServiceCars;
import co.edu.grocerystore.api.ServiceUsers;
import co.edu.grocerystore.model.Car;
import co.edu.grocerystore.model.ResponseCar;
import co.edu.grocerystore.model.ResponseMessage;
import co.edu.grocerystore.model.ResponseUser;
import co.edu.grocerystore.model.User;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class bill extends AppCompatActivity {
    private RecyclerView rcProducts;
    private Button btnReturn;
    private TextView factNum;
    private TextView billName;
    private TextView billAddress;
    private TextView billPhone;
    private Retrofit retrofit;
    private ArrayList<Car> cars = new ArrayList<Car>();
    private User currentUser = new User();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_bill);
        begin();
        getUserById(getAuth(), getUserId());
        getProducts(getUserId(), getAuth());
        btnReturn.setOnClickListener(this::catalog);
    }

    private void catalog(View view) {
        clearCar();
        Intent store = new Intent(getApplicationContext(), store.class);
        startActivity(store);
    }

    private void clearCar(){
        for(int i = 0; i < cars.size(); i++){
            String pdId = cars.get(i).getProduct_id();
            removeProducts(getAuth(), pdId);
        }
    }

    private void removeProducts(String auth, String productId){
        retrofit = ClientRetroFit.getClient(BASE_URL);
        ServiceCars serviceCars = retrofit.create(ServiceCars.class);
        Call<ResponseMessage> call = serviceCars.deleteCar(productId, auth);
        call.enqueue(new Callback<ResponseMessage>() {
            @Override
            public void onResponse(Call<ResponseMessage> call, Response<ResponseMessage> response) {

            }

            @Override
            public void onFailure(Call<ResponseMessage> call, Throwable t) {

            }
        });
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

    private String getAuth(){
        SharedPreferences preferences = getApplicationContext().getSharedPreferences("credenciales", Context.MODE_PRIVATE);
        String key = preferences.getString("key","No");
        String identifier = preferences.getString("identifier","No");
        String token = identifier + ":" + key;
        final String AUTH = "Basic " + Base64.encodeToString((token).getBytes(), NO_WRAP);
        return AUTH;
    }

    private String getUserId(){
        SharedPreferences preferences = getApplicationContext().getSharedPreferences("credenciales", Context.MODE_PRIVATE);
        String id = preferences.getString("id","NoID");
        return id;
    }

    public void getProducts(String id, String auth){
        retrofit = ClientRetroFit.getClient(BASE_URL);
        ServiceCars serviceCars = retrofit.create(ServiceCars.class);
        Call<ResponseCar> call = serviceCars.getCars(id, auth);
        call.enqueue(new Callback<ResponseCar>() {
            @Override
            public void onResponse(Call<ResponseCar> call, Response<ResponseCar> response) {
                ResponseCar body = response.body();
                ArrayList<Car> list = body.getCars();
                if(!isNullOrEmpty(list)){
                    cars = list;
                    billAdapter aCar = new billAdapter(list);
                    rcProducts.setAdapter(aCar);
                }
            }

            @Override
            public void onFailure(Call<ResponseCar> call, Throwable t) {
                Toast.makeText(getApplicationContext(), "Error getting cars", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void getUserById(String auth, String id){
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
                    billName.setText("Cliente: " + currentUser.getUser_name() + " " +currentUser.getUser_lastname());
                    billAddress.setText("Dirección: " + currentUser.getUser_address());
                    billPhone.setText("Teléfono:" + currentUser.getUser_phone());
                } else {
                    Toast.makeText(getApplicationContext(), "No error", Toast.LENGTH_SHORT).show();
                    Log.i("ERROR1", "onResponse: "+response.body());
                }
            }

            @Override
            public void onFailure(Call<ResponseUser> call, Throwable t) {
                Toast.makeText(getApplicationContext(), "error:"+t.getMessage(), Toast.LENGTH_SHORT).show();
                Log.i("ERROR2", "onFailure: "+ t.getMessage());
            }
        });
    }

    private void begin(){
        this.rcProducts = findViewById(R.id.rcBill);
        rcProducts.setLayoutManager(new LinearLayoutManager(getApplicationContext()));
        this.btnReturn =findViewById(R.id.btnBillReturn);
        this.billName = findViewById(R.id.billName);
        this.billAddress = findViewById(R.id.billAddress);
        this.billPhone = findViewById(R.id.billPhone);
    }
}