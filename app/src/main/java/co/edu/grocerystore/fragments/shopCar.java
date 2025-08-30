package co.edu.grocerystore.fragments;

import static android.util.Base64.NO_WRAP;
import static co.edu.grocerystore.api.ValuesApi.BASE_URL;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.Bundle;

import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.util.Base64;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.Collection;

import co.edu.grocerystore.R;
import co.edu.grocerystore.adapters.carAdapter;
import co.edu.grocerystore.adapters.transactionAdapter;
import co.edu.grocerystore.api.ServiceCars;
import co.edu.grocerystore.model.Car;
import co.edu.grocerystore.model.ResponseCar;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class shopCar extends Fragment {
    private RecyclerView carRecycler;
    private Retrofit retrofit;
    private Button goTransaction;
    private ArrayList<Car> cars = new ArrayList<Car>();
    public shopCar() {
        // Required empty public constructor
    }

    public static shopCar newInstance() {
        shopCar fragment = new shopCar();
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
        View view = inflater.inflate(R.layout.fragment_shop_car, container, false);
        begin(view);
        getProducts(getUserId(),getAuth());
        goTransaction.setOnClickListener(this::transaction);
        return view;
    }

    public void loadFragment(Fragment fragment){
        FragmentManager fragmentManager = getActivity().getSupportFragmentManager();
        FragmentTransaction transaction = fragmentManager.beginTransaction();
        transaction.replace(R.id.frame_container,fragment).addToBackStack(null).commit();
    }

    private void transaction(View view) {
        if(cars.size() > 0){
            Fragment transact = new transaction();
            loadFragment(transact);
        } else {
            Toast.makeText(getActivity(), "No tienes productos en el carrito", Toast.LENGTH_SHORT).show();
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
                    carAdapter aCar = new carAdapter(list);
                    carRecycler.setAdapter(aCar);
                }
            }

            @Override
            public void onFailure(Call<ResponseCar> call, Throwable t) {
                Toast.makeText(getActivity(), "Error getting cars", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void begin(View view){
        this.carRecycler = view.findViewById(R.id.carRecycler);
        carRecycler.setLayoutManager(new LinearLayoutManager(getContext()));
        this.goTransaction = view.findViewById(R.id.btnProceed);
    }
}