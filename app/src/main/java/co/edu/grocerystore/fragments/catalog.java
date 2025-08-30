package co.edu.grocerystore.fragments;

import static android.util.Base64.NO_WRAP;

import static co.edu.grocerystore.api.ValuesApi.BASE_URL;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.Bundle;

import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.util.Base64;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.material.snackbar.Snackbar;

import java.util.ArrayList;
import java.util.Collection;

import co.edu.grocerystore.R;
import co.edu.grocerystore.adapters.productAdapter;
import co.edu.grocerystore.api.ServiceProducts;
import co.edu.grocerystore.model.Products;
import co.edu.grocerystore.model.ResponseProduct;
import co.edu.grocerystore.model.ResponseUser;
import co.edu.grocerystore.remote.ClientRetroFit;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

/**
 * A simple {@link Fragment} subclass.
 * Use the {@link catalog#newInstance} factory method to
 * create an instance of this fragment.
 */
public class catalog extends Fragment {
    private RecyclerView paperRecycler;
    private RecyclerView writingRecycler;
    private RecyclerView scholarRecycler;
    private RecyclerView officeRecycler;
    private RecyclerView archiveRecycler;
    private Retrofit retrofit;
    private ArrayList<Products> prsPaper = new ArrayList<Products>();
    private ArrayList<Products> prsWriting = new ArrayList<Products>();
    private ArrayList<Products> prsScholar = new ArrayList<Products>();
    private ArrayList<Products> prsOffice = new ArrayList<Products>();
    private ArrayList<Products> prsArchive = new ArrayList<Products>();

    public catalog() {
        // Required empty public constructor
    }

    public static catalog newInstance() {
        catalog fragment = new catalog();
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
        View view = inflater.inflate(R.layout.fragment_catalog, container, false);
        begin(view);
        getProducts(getAuth());
        return view;
    }

    private String getAuth(){
        SharedPreferences preferences = getActivity().getSharedPreferences("credenciales", Context.MODE_PRIVATE);
        String key = preferences.getString("key","No");
        String identifier = preferences.getString("identifier","No");
        String token = identifier + ":" + key;
        final String AUTH = "Basic " + Base64.encodeToString((token).getBytes(), NO_WRAP);
        return AUTH;
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

    private void getProducts(String auth){
        retrofit = ClientRetroFit.getClient(BASE_URL);
        ServiceProducts serviceProducts = retrofit.create(ServiceProducts.class);
        Call<ResponseProduct> call = serviceProducts.getProductsByCategory(auth);
        call.enqueue(new Callback<ResponseProduct>() {
            @Override
            public void onResponse(Call<ResponseProduct> call, Response<ResponseProduct> response) {
                ResponseProduct body = response.body();
                ArrayList<Products> list = body.getProducts();
                if(!isNullOrEmpty(list)){
                    setCategoryProducts(list);
                    setAdapters();
                }
            }

            @Override
            public void onFailure(Call<ResponseProduct> call, Throwable t) {
                Toast.makeText(getActivity(), "Error getting products", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void setAdapters(){
        productAdapter aPaper = new productAdapter(prsPaper);
        productAdapter aWrite = new productAdapter(prsWriting);
        productAdapter aScho = new productAdapter(prsScholar);
        productAdapter aOffi = new productAdapter(prsOffice);
        productAdapter aArch = new productAdapter(prsArchive);
        paperRecycler.setAdapter(aPaper);
        scholarRecycler.setAdapter(aScho);
        writingRecycler.setAdapter(aWrite);
        officeRecycler.setAdapter(aOffi);
        archiveRecycler.setAdapter(aArch);
    }

    private void setCategoryProducts(ArrayList<Products> products){
        for (int i = 0; i < products.size(); i++){
            int categoryId = products.get(i).getCategory_id();
            switch (categoryId){
                case 1:
                    prsPaper.add(products.get(i));
                    break;
                case 2:
                    prsWriting.add(products.get(i));
                    break;
                case 3:
                    prsScholar.add(products.get(i));
                    break;
                case 4:
                    prsOffice.add(products.get(i));
                    break;
                case 5:
                    prsArchive.add(products.get(i));
                    break;
            }
        }
    }

    public void begin(View view){
        this.paperRecycler = view.findViewById(R.id.paperRecycler);
        this.writingRecycler = view.findViewById(R.id.writingRecycler);
        this.scholarRecycler = view.findViewById(R.id.scholarRecycler);
        this.officeRecycler = view.findViewById(R.id.officeRecycler);
        this.archiveRecycler = view.findViewById(R.id.archiveRecycler);
        paperRecycler.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.HORIZONTAL, false));
        writingRecycler.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.HORIZONTAL, false));
        scholarRecycler.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.HORIZONTAL, false));
        officeRecycler.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.HORIZONTAL, false));
        archiveRecycler.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.HORIZONTAL, false));
    }
}