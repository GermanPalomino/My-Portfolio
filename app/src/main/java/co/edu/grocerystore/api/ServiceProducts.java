package co.edu.grocerystore.api;

import co.edu.grocerystore.model.ResponseProduct;
import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Path;

public interface ServiceProducts {
    @GET("products/{id}")
    public Call<ResponseProduct> getProductsByCategory(@Header("Authorization") String token);
}
