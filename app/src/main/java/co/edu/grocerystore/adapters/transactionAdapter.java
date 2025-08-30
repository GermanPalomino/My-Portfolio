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

public class transactionAdapter extends RecyclerView.Adapter<transactionAdapter.vh> {
    List<Car> carList;

    public transactionAdapter(List<Car> carList){
        this.carList = carList;
    }

    @NonNull
    @Override
    public transactionAdapter.vh onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.transact_layout, null, false);
        return new vh(view);
    }

    @Override
    public void onBindViewHolder(@NonNull transactionAdapter.vh holder, int position) {
        holder.productName.setText(carList.get(position).getProduct_name());
        holder.productQuantity.setText("Cantidad: " + carList.get(position).getProduct_quantity());
    }

    @Override
    public int getItemCount() {
        return carList.size();
    }

    public class vh extends RecyclerView.ViewHolder {
        TextView productName;
        TextView productQuantity;
        public vh(@NonNull View itemView) {
            super(itemView);
            productName = itemView.findViewById(R.id.tvTrName);
            productQuantity = itemView.findViewById(R.id.tvTrQuantity);
        }
    }
}
