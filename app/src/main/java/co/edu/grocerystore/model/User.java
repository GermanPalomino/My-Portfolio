package co.edu.grocerystore.model;

import java.io.Serializable;

public class User implements Serializable {
    private int user_id;
    private String user_name;
    private String user_lastname;
    private String user_phone;
    private String user_doctype;
    private String user_docnumber;
    private String user_email;
    private String user_address;

    public int getUser_id() {
        return user_id;
    }

    public void setUser_id(int user_id) {
        this.user_id = user_id;
    }

    public String getUser_name() {
        return user_name;
    }

    public void setUser_name(String user_name) {
        this.user_name = user_name;
    }

    public String getUser_lastname() {
        return user_lastname;
    }

    public void setUser_lastname(String user_lastname) {
        this.user_lastname = user_lastname;
    }

    public String getUser_phone() {
        return user_phone;
    }

    public void setUser_phone(String user_phone) {
        this.user_phone = user_phone;
    }

    public String getUser_doctype() {
        return user_doctype;
    }

    public void setUser_doctype(String user_doctype) {
        this.user_doctype = user_doctype;
    }

    public String getUser_docnumber() {
        return user_docnumber;
    }

    public void setUser_docnumber(String user_docnumber) {
        this.user_docnumber = user_docnumber;
    }

    public String getUser_email() {
        return user_email;
    }

    public void setUser_email(String user_email) {
        this.user_email = user_email;
    }

    public String getUser_address() {
        return user_address;
    }

    public void setUser_address(String user_address) {
        this.user_address = user_address;
    }
}
