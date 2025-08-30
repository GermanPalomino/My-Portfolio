package co.edu.grocerystore.adapters;

import static android.util.Base64.NO_WRAP;

import static co.edu.grocerystore.api.ValuesApi.BASE_URL;

import android.content.Context;
import android.content.SharedPreferences;
import android.util.Base64;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

import co.edu.grocerystore.R;
import co.edu.grocerystore.api.ServiceCars;
import co.edu.grocerystore.model.Car;
import co.edu.grocerystore.model.Products;
import co.edu.grocerystore.model.ResponseCredentials;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class productAdapter extends RecyclerView.Adapter<productAdapter.vh>{
    List<Products> productList;
    private Retrofit retroFit;

    public interface MyClickListener{
        void onAddCar(int p);
    }
    public productAdapter(List<Products> productList) {
        this.productList = productList;
    }

    @NonNull
    @Override
    public productAdapter.vh onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.product_layout, null, false);
        vh holder = new vh(view, new MyClickListener() {
            @Override
            public void onAddCar(int p) {
                String productId = "" + productList.get(p).getProduct_id();
                String productName = productList.get(p).getProduct_name();
                int productPrice = productList.get(p).getProduct_price();
                addCar(view, getUserId(view), productId, productName, productPrice, getAuth(view));
            }
        });
        return holder;
    }

    private String getAuth(View v){
        SharedPreferences preferences = v.getContext().getSharedPreferences("credenciales", Context.MODE_PRIVATE);
        String key = preferences.getString("key","No");
        String identifier = preferences.getString("identifier","No");
        String token = identifier + ":" + key;
        final String AUTH = "Basic " + Base64.encodeToString((token).getBytes(), NO_WRAP);
        return AUTH;
    }

    private String getUserId(View v){
        SharedPreferences preferences = v.getContext().getSharedPreferences("credenciales", Context.MODE_PRIVATE);
        String id = preferences.getString("id","NoID");
        return id;
    }

    private void addCar(View v, String userId, String productId, String productName, int productPrice, String auth){
        Car car = new Car();
        car.setUser_id(userId);
        car.setProduct_id(productId);
        car.setProduct_name(productName);
        car.setProduct_quantity(1);
        car.setProduct_price(productPrice);
        retroFit = ClientRetroFit.getClient(BASE_URL);
        ServiceCars serviceCars = retroFit.create(ServiceCars.class);
        Call<ResponseCredentials> call = serviceCars.addCar(car, auth);
        call.enqueue(new Callback<ResponseCredentials>() {
            @Override
            public void onResponse(Call<ResponseCredentials> call, Response<ResponseCredentials> response) {
                if(response.isSuccessful()){
                    Toast.makeText(v.getContext(), productName + " añadido al carrito", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<ResponseCredentials> call, Throwable t) {
                Toast.makeText(v.getContext(), "error", Toast.LENGTH_SHORT).show();
            }
        });
    }

    @Override
    public void onBindViewHolder(@NonNull productAdapter.vh holder, int position) {
        holder.productName.setText(productList.get(position).getProduct_name());
        holder.productPrice.setText("$" +productList.get(position).getProduct_price());
    }

    @Override
    public int getItemCount() {
        return productList.size();
    }

    public class vh extends RecyclerView.ViewHolder implements View.OnClickListener{
        TextView productName;
        TextView productPrice;
        Button btnAddCar;
        MyClickListener listener;
        public vh(@NonNull View itemView, MyClickListener listener) {
            super(itemView);
            productName = itemView.findViewById(R.id.tvProductName);
            productPrice = itemView.findViewById(R.id.tvProductPrice);
            btnAddCar = itemView.findViewById(R.id.btnAddCar);
            this.listener = listener;
            btnAddCar.setOnClickListener(this);
        }

        @Override
        public void onClick(View v) {
            listener.onAddCar(this.getLayoutPosition());
        }
    }
}
