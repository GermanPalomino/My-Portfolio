package co.edu.grocerystore.model;

import java.util.ArrayList;

public class ResponseCredentials {
    private ArrayList<Credentials> credentials;
    private String message;

    public ResponseCredentials(){ }

    public ArrayList<Credentials> getCredentials() {
        return credentials;
    }

    public void setCredentials(ArrayList<Credentials> credentials) {
        this.credentials = credentials;
    }

    public String getMessage() {
        return message;
    }

    public void setMensaje(String message) {
        this.message = message;
    }
}
