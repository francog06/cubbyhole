package com.supinfo.cubbyhole.mobileapp.activities;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.supinfo.cubbyhole.mobileapp.R;
import com.supinfo.cubbyhole.mobileapp.models.User;
import com.supinfo.cubbyhole.mobileapp.utils.Utils;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by anthonyvialleton on 04/04/14.
 */

public class LoginActivity extends ActionBarActivity {

    private ProgressBar pb;
    private EditText mailET;
    private EditText passwordET;
    private Button loginBtn;
    private Button registerBtn;
    private TextView forgotTV;
    private String username = "";
    private String password = "";


    @Override
    protected void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        pb = (ProgressBar) findViewById(R.id.login_pb);
        pb.setVisibility(View.GONE);
        forgotTV = (TextView)findViewById(R.id.login_forget);

        mailET = (EditText) findViewById(R.id.login_username_edittext);
        passwordET = (EditText) findViewById(R.id.login_password_edittext);
        loginBtn = (Button) findViewById(R.id.login_btn);
        registerBtn = (Button) findViewById(R.id.login_register_btn);

        loginBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
            	
            	if (Utils.IsNetworkAvailable(LoginActivity.this)){
            	
	                LoginActivity.this.username = mailET.getText().toString();
	                LoginActivity.this.password = passwordET.getText().toString();
	
	                if (!LoginActivity.this.username.equalsIgnoreCase("") &&
	                        !LoginActivity.this.password.equalsIgnoreCase(""))
	                {
	                    new Login().execute();
	                }
	                else
	                {
	                    Toast.makeText(LoginActivity.this, R.string.error_loginblank, Toast.LENGTH_LONG).show();
	                }
            	}else{
            		Toast.makeText(LoginActivity.this, R.string.error_nointernet, Toast.LENGTH_LONG).show();
            	}
            }
        });

        registerBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent_to_register = new Intent(LoginActivity.this, RegisterActivity.class);
                startActivityForResult(intent_to_register, 10);
            }
        });

        forgotTV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent_to_retrievepassword = new Intent(LoginActivity.this, RetrievePasswordActivity.class);
                startActivityForResult(intent_to_retrievepassword, 10);
            }
        });
    }

    @Override
    protected void onResume() {
        super.onResume();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
    }

    private class Login extends AsyncTask<Void, Integer, Boolean> {

        private User user;

        public Login() {}

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            pb.setVisibility(View.VISIBLE);
            loginBtn.setEnabled(false);
        }

        protected Boolean doInBackground(Void... params) {

            try {

                // Envoi des params utilisateur en attente d'une reponse par l'API

                // Ajout des donnees
                List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(2);
                nameValuePairs.add(new BasicNameValuePair(Utils.JSON_USER_EMAIL, mailET.getText().toString()));
                nameValuePairs.add(new BasicNameValuePair(Utils.JSON_USER_PASSWORD, passwordET.getText().toString()));
                
                user = Utils.LoginPostHTTP(Utils.USER_LOGIN_URL, nameValuePairs);

            } catch (Exception e) {
                Log.w(getClass().getSimpleName(), "exception Connect : Json");
                e.printStackTrace();
            }

            return true;
        }

        @Override
        protected void onPostExecute(Boolean downloadedArray) {
            super.onPostExecute(downloadedArray);

            if (user != null)// Login OK
            {
                Utils.setUserFromSharedPreferences(LoginActivity.this, user);

                Intent intent_to_home = new Intent(LoginActivity.this, Home.class);
                startActivity(intent_to_home);
                finish();
            }
            else // Login No OK
            {
                loginBtn.setEnabled(true);
                if (pb!=null){pb.setVisibility(View.GONE);}
                Toast.makeText(LoginActivity.this, R.string.error_login, Toast.LENGTH_LONG).show();
            }

        }

    }



}
