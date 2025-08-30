package co.edu.grocerystore.api;

import co.edu.grocerystore.model.Loger;
import co.edu.grocerystore.model.ResponseCredentials;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.POST;

public interface ServiceLogin {
    @POST("login")
    public Call<ResponseCredentials> accesLogin(@Body Loger login);
}
