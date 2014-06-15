package com.supinfo.cubbyhole.mobileapp.activities;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;
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

public class RegisterActivity extends ActionBarActivity {

    private ProgressBar pb;
    private EditText mailET;
    private EditText passwordET;
    private Button registerBtn;
    private String username = "";
    private String password = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        
        pb = (ProgressBar) findViewById(R.id.register_pb);
        pb.setVisibility(View.GONE);
        mailET = (EditText) findViewById(R.id.register_username_edittext);
        passwordET = (EditText) findViewById(R.id.register_password_edittext);
        registerBtn = (Button) findViewById(R.id.register_btn);

        registerBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                RegisterActivity.this.username = mailET.getText().toString();
                RegisterActivity.this.password = passwordET.getText().toString();

                if (!RegisterActivity.this.username.equalsIgnoreCase("") &&
                        !RegisterActivity.this.password.equalsIgnoreCase(""))
                {
                    new Register().execute();
                }
                else
                {
                    Toast.makeText(RegisterActivity.this, R.string.error_loginblank, Toast.LENGTH_LONG).show();
                }
            }
        });
    }
    
    @Override
   	public boolean onOptionsItemSelected(MenuItem item) {
   		switch (item.getItemId()) {
   		case android.R.id.home:
   			Intent mIntent = new Intent(this, LoginActivity.class);
   			setResult(11, mIntent);
   			finish();
   			return true;
   		default:
   			return super.onOptionsItemSelected(item);
   		}
   	}
    
    @Override
    protected void onResume() {
        super.onResume();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
    }

    private class Register extends AsyncTask<Void, Integer, Boolean> {

        private User user;

        public Register() {}

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            pb.setVisibility(View.VISIBLE);
            registerBtn.setEnabled(false);
        }

        protected Boolean doInBackground(Void... params) {

            try {

                // Envoi des params utilisateur en attente d'une reponse par l'API

                // Ajout des donnees
                List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(2);
                nameValuePairs.add(new BasicNameValuePair(Utils.JSON_USER_EMAIL, mailET.getText().toString()));
                nameValuePairs.add(new BasicNameValuePair(Utils.JSON_USER_PASSWORD, passwordET.getText().toString()));

                user = Utils.RegisterPostHTTP(Utils.USER_REGISTRATION_URL, nameValuePairs);

            } catch (Exception e) {
                Log.w(getClass().getSimpleName(), "exception Register : Json");
                e.printStackTrace();
            }

            return true;
        }

        @Override
        protected void onPostExecute(Boolean downloadedArray) {
            super.onPostExecute(downloadedArray);

            if (user != null)
            {
                if (user.getId() == -1)// Register No OK because invalid mail
                {
                    registerBtn.setEnabled(true);
                    mailET.getText().clear();
                    passwordET.getText().clear();
                    if (pb!=null){pb.setVisibility(View.GONE);}
                    Toast.makeText(RegisterActivity.this, user.getError(), Toast.LENGTH_LONG).show();
                }
                else // Register OK
                {
                    Utils.setUserFromSharedPreferences(RegisterActivity.this, user);

                    Intent intent_to_home = new Intent(RegisterActivity.this, Home.class);
           			setResult(11, intent_to_home);
           			finish();
           			Toast.makeText(RegisterActivity.this, "Compte parfaitement créé! Connectez vous et profitez rapidement du service CubbyHole!", Toast.LENGTH_LONG).show();
                }
            }
            else // Register No OK
            {
                registerBtn.setEnabled(true);
                mailET.getText().clear();
                passwordET.getText().clear();
                if (pb!=null){pb.setVisibility(View.GONE);}
                Toast.makeText(RegisterActivity.this, R.string.error_register, Toast.LENGTH_LONG).show();
            }

        }

    }

}
