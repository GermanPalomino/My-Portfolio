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
import co.edu.grocerystore.model.ResponseCar;
import co.edu.grocerystore.model.ResponseCredentials;
import co.edu.grocerystore.model.ResponseMessage;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class carAdapter extends RecyclerView.Adapter<carAdapter.vh> {
    List<Car> carList;
    private Retrofit retrofit;
    private int quantity;

    public interface MyClickListener{
        void onDecrease(int p);
        void onIncrease(int p);
        void onRemove(int p);
    }

    public carAdapter(List<Car> carList){
        this.carList = carList;
    }

    @NonNull
    @Override
    public carAdapter.vh onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.car_layout, null, false);
        vh holder = new vh(view, new MyClickListener() {
            @Override
            public void onDecrease(int p) {}

            @Override
            public void onIncrease(int p) {}

            @Override
            public void onRemove(int p) {

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

    private void updateCar(View v, String auth, Car updateCar){
        Car car = new Car();
        car = updateCar;
        retrofit = ClientRetroFit.getClient(BASE_URL);
        ServiceCars serviceCars = retrofit.create(ServiceCars.class);
        Call<ResponseCredentials> call = serviceCars.updateCar(car.getCar_id(), auth, car);
        call.enqueue(new Callback<ResponseCredentials>() {
            @Override
            public void onResponse(Call<ResponseCredentials> call, Response<ResponseCredentials> response) {

            }

            @Override
            public void onFailure(Call<ResponseCredentials> call, Throwable t) {
                Toast.makeText(v.getContext(), "error", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void removeProduct(String auth, View view, String productId){
        retrofit = ClientRetroFit.getClient(BASE_URL);
        ServiceCars serviceCars = retrofit.create(ServiceCars.class);
        Call<ResponseMessage> call = serviceCars.deleteCar(productId, auth);
        call.enqueue(new Callback<ResponseMessage>() {
            @Override
            public void onResponse(Call<ResponseMessage> call, Response<ResponseMessage> response) {
                if(response.isSuccessful()){
                    Toast.makeText(view.getContext(), "Producto removido", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(view.getContext(), "Producto no removido", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<ResponseMessage> call, Throwable t) {
                Toast.makeText(view.getContext(), "Error", Toast.LENGTH_SHORT).show();
            }
        });
    }

    @Override
    public void onBindViewHolder(@NonNull carAdapter.vh holder, int position) {
        Car car = carList.get(position);
        holder.prName.setText(car.getProduct_name());
        this.quantity = car.getProduct_quantity();
        holder.prQuantity.setText("Cantidad: " + quantity);
        holder.btnIncrease.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                quantity += 1;
                car.setProduct_quantity(quantity);
                holder.prQuantity.setText("Cantidad: " + quantity);
                updateCar(holder.itemView,getAuth(holder.itemView),car);
            }
        });

        holder.btnDecrease.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(quantity > 1){
                    quantity -= 1;
                    car.setProduct_quantity(quantity);
                    holder.prQuantity.setText("Cantidad: " + quantity);
                    updateCar(holder.itemView,getAuth(holder.itemView),car);
                } else {
                    Toast.makeText(holder.itemView.getContext(), "Esta es la minima cantidad de productos, si lo deseas eliminalo.", Toast.LENGTH_SHORT).show();
                }
            }
        });

        holder.btnRemove.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                removeProduct(getAuth(holder.itemView), holder.itemView, car.getProduct_id());
                carList.remove(car);
                notifyItemRemoved(holder.getAdapterPosition());
                notifyItemRangeChanged(holder.getAdapterPosition(), carList.size());
            }
        });
    }

    @Override
    public int getItemCount() {
        return carList.size();
    }

    public static class vh extends RecyclerView.ViewHolder implements View.OnClickListener{
        TextView prName;
        Button btnDecrease;
        TextView prQuantity;
        Button btnIncrease;
        Button btnRemove;
        MyClickListener listener;
        public vh(@NonNull View itemView, MyClickListener listener) {
            super(itemView);
            prName = itemView.findViewById(R.id.tvPrName);
            btnDecrease = itemView.findViewById(R.id.btnDecrease);
            prQuantity = itemView.findViewById(R.id.prQuantity);
            btnIncrease = itemView.findViewById(R.id.btnIncrease);
            btnRemove = itemView.findViewById(R.id.btnRemoveProduct);
            this.listener = listener;
            btnDecrease.setOnClickListener(this);
            btnIncrease.setOnClickListener(this);
            btnRemove.setOnClickListener(this);
        }

        @Override
        public void onClick(View v) {
            int id = R.id.btnDecrease;
            int id2 = R.id.btnIncrease;
            int id3 = R.id.btnRemoveProduct;
            if(v.getId() == id){
                listener.onDecrease(this.getLayoutPosition());
            } else if (v.getId() == id2) {
                listener.onIncrease(this.getLayoutPosition());
            } else if (v.getId() == id3){
                listener.onRemove(this.getLayoutPosition());
            }
        }
    }
}
