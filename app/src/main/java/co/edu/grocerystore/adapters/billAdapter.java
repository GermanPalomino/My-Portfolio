package co.edu.grocerystore.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

import co.edu.grocerystore.R;
import co.edu.grocerystore.model.Car;

public class billAdapter extends RecyclerView.Adapter<billAdapter.vh> {
    List<Car> carList;

    public billAdapter(List<Car> carList){
        this.carList = carList;
    }

    @NonNull
    @Override
    public billAdapter.vh onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.bill_layout, null, false);
        return new vh(view);
    }

    @Override
    public void onBindViewHolder(@NonNull billAdapter.vh holder, int position) {
        Car car = carList.get(position);
        int total = car.getProduct_quantity() * car.getProduct_price();
        holder.prDescription.setText(car.getProduct_name());
        holder.prQuantity.setText(""+car.getProduct_quantity());
        holder.prPrice.setText("$" + car.getProduct_price());
        holder.prTotal.setText("$" + total);
    }

    @Override
    public int getItemCount() {
        return carList.size();
    }

    public class vh extends RecyclerView.ViewHolder {
        TextView prDescription;
        TextView prQuantity;
        TextView prPrice;
        TextView prTotal;
        public vh(@NonNull View itemView) {
            super(itemView);
            prDescription = itemView.findViewById(R.id.tvPrDes);
            prQuantity = itemView.findViewById(R.id.tvPrQua);
            prPrice = itemView.findViewById(R.id.tvPrPrice);
            prTotal = itemView.findViewById(R.id.tvPrTotal);
        }
    }
}
