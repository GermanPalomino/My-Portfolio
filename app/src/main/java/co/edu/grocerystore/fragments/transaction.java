package co.edu.grocerystore.fragments;

import static android.util.Base64.NO_WRAP;

import static co.edu.grocerystore.api.ValuesApi.BASE_URL;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;

import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.util.Base64;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.Collection;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import co.edu.grocerystore.R;
import co.edu.grocerystore.adapters.transactionAdapter;
import co.edu.grocerystore.api.ServiceCars;
import co.edu.grocerystore.api.ServiceUsers;
import co.edu.grocerystore.bill;
import co.edu.grocerystore.model.Car;
import co.edu.grocerystore.model.ResponseCar;
import co.edu.grocerystore.model.ResponseUser;
import co.edu.grocerystore.model.User;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

/**
 * A simple {@link Fragment} subclass.
 * Use the {@link transaction#newInstance} factory method to
 * create an instance of this fragment.
 */
public class transaction extends Fragment {
    private TextView tvAddress;
    private Spinner spPayMethod;
    private EditText etAccountNum;
    private RecyclerView ltProducts;
    private TextView tvTotalPr;
    private TextView tvSendPrice;
    private TextView tvTotal;
    private Button btnConfirmPay;
    private ImageView imageReturn;
    private Retrofit retrofit;
    private User currentUser = new User();
    private ArrayList<Car> cars = new ArrayList<Car>();

    public transaction() {
        // Required empty public constructor
    }

    public static transaction newInstance() {
        transaction fragment = new transaction();
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
        View view = inflater.inflate(R.layout.fragment_transaction, container, false);
        begin(view);
        getUserById(getAuth(), getUserId());
        getCars(getUserId(), getAuth());
        btnConfirmPay.setOnClickListener(this::goBill);
        imageReturn.setOnClickListener(this::shopCar);
        return view;
    }

    private void goBill(View view) {
        if(validations()){
            Intent goBill = new Intent(getContext(), bill.class);
            startActivity(goBill);
        }
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

    public static boolean isNullOrEmpty(Object obj){
        if(obj==null)return true;
        if(obj instanceof String) return ((String)obj).trim().equals("") || ((String)obj).equalsIgnoreCase("NULL");
        if(obj instanceof Integer) return ((Integer)obj)==0;
        if(obj instanceof Long) return ((Long)obj).equals(new Long(0));
        if(obj instanceof Double) return ((Double)obj).equals(0.0);
        if(obj instanceof Collection) return (((Collection)obj).isEmpty());
        return false;
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
                    tvAddress.setText(currentUser.getUser_address());
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

    private void getCars(String userId, String auth){
        retrofit = ClientRetroFit.getClient(BASE_URL);
        ServiceCars serviceCars = retrofit.create(ServiceCars.class);
        Call<ResponseCar> call = serviceCars.getCars(userId, auth);
        call.enqueue(new Callback<ResponseCar>() {
            @Override
            public void onResponse(Call<ResponseCar> call, Response<ResponseCar> response) {
                ResponseCar body = response.body();
                ArrayList<Car> list = body.getCars();
                if(!isNullOrEmpty(list)){
                    setTotals(list);
                    cars = list;
                    transactionAdapter aTransaction = new transactionAdapter(list);
                    ltProducts.setAdapter(aTransaction);
                }
            }

            @Override
            public void onFailure(Call<ResponseCar> call, Throwable t) {
                Toast.makeText(getActivity(), "Error", Toast.LENGTH_SHORT).show();
            }
        });
    }
    public void loadFragment(Fragment fragment){
        FragmentManager fragmentManager = getActivity().getSupportFragmentManager();
        FragmentTransaction transaction = fragmentManager.beginTransaction();
        transaction.replace(R.id.frame_container,fragment).addToBackStack(null).commit();
    }
    private void shopCar(View view){
        Fragment shopCar = new shopCar();
        loadFragment(shopCar);
    }

    private void setTotals(ArrayList<Car> carList){
        int totalPr = 0;
        int totalSen = 5000;
        int totalTransact = 0;
        for(int i = 0; i < carList.size(); i++){
            int quantity = carList.get(i).getProduct_quantity();
            int price = carList.get(i).getProduct_price();
            int total = quantity * price;
            totalPr += total;
        }
        totalTransact = totalPr + totalSen;
        tvTotalPr.setText("Productos: $" + totalPr);
        tvSendPrice.setText("Envío: $" + totalSen);
        tvTotal.setText("Total: $" + totalTransact);
    }

    private boolean validations(){
        String numAccount = etAccountNum.getText().toString();
        if(validatePhoneNumber(numAccount)){
            return true;
        } else if (numAccount.isEmpty()){
            Toast.makeText(getActivity(), "El número de cuenta no puede estar vacío", Toast.LENGTH_SHORT).show();
            return false;
        } else {
            Toast.makeText(getActivity(), "El número de cuenta debe tener 10 digitos", Toast.LENGTH_SHORT).show();
            return false;
        }
    }

    private boolean validatePhoneNumber(String input){
        Pattern regex = Pattern.compile("^[0-9]{10}$");
        Matcher matchs = regex.matcher(input);
        return matchs.matches();
    }

    private void begin(View v){
        this.tvAddress = v.findViewById(R.id.tvAddress);
        this.spPayMethod = v.findViewById(R.id.spPayMethod);
        this.etAccountNum = v.findViewById(R.id.etAccNum);
        this.ltProducts = v.findViewById(R.id.ltProducts);
        ltProducts.setLayoutManager(new LinearLayoutManager(getContext()));
        this.tvTotalPr = v.findViewById(R.id.tvTotalProducts);
        this.tvSendPrice = v.findViewById(R.id.tvSendPrice);
        this.tvTotal = v.findViewById(R.id.tvTotalTransaction);
        this.btnConfirmPay = v.findViewById(R.id.btnConfirmTransaction);
        this.imageReturn = v.findViewById(R.id.transactReturn);
    }
}