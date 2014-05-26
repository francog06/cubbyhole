package com.supinfo.cubbyhole.mobileapp.activities;

import android.app.Activity;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.Toast;

import com.supinfo.cubbyhole.mobileapp.R;
import com.supinfo.cubbyhole.mobileapp.utils.Utils;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by anthonyvialleton on 04/04/14.
 */

public class RetrievePasswordActivity extends Activity {

    private EditText mailET;
    private Button sendBtn;
    private String mail = "";
    private ProgressBar pb;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_retrievepassword);

        pb = (ProgressBar)findViewById(R.id.retrieve_pb);
        pb.setVisibility(View.GONE);
        mailET = (EditText)findViewById(R.id.retrieve_mail_edittext);
        sendBtn = (Button) findViewById(R.id.retrieve_send_btn);

        sendBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                new Retrieve().execute();
            }
        });

    }



    private class Retrieve extends AsyncTask<Void, Integer, Boolean> {

        Boolean mailSend = false;

        public Retrieve() {}

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            pb.setVisibility(View.VISIBLE);
            sendBtn.setEnabled(false);
        }

        protected Boolean doInBackground(Void... params) {

            try {

                // Envoi des params utilisateur en attente d'une réponse par l'API

                // Ajout des données
                List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(2);
                nameValuePairs.add(new BasicNameValuePair(Utils.JSON_USER_EMAIL, mailET.getText().toString()));

                mailSend = Utils.RetrievePostHTTP(Utils.USER_RETRIEVE_URL, nameValuePairs);

            } catch (Exception e) {
                Log.w(getClass().getSimpleName(), "exception Retrieve : Json");
                e.printStackTrace();
            }

            return true;
        }

        @Override
        protected void onPostExecute(Boolean downloadedArray) {
            super.onPostExecute(downloadedArray);

            if (mailSend)// Retrieve OK
            {
                Intent intent_to_login = new Intent(RetrievePasswordActivity.this, LoginActivity.class);
                startActivity(intent_to_login);
                finish();
                Toast.makeText(RetrievePasswordActivity.this, R.string.done_retrieve, Toast.LENGTH_LONG).show();
            }
            else // Retrieve No OK
            {
                sendBtn.setEnabled(true);
                if (pb!=null){pb.setVisibility(View.GONE);}
                Toast.makeText(RetrievePasswordActivity.this, R.string.error_retrieve, Toast.LENGTH_LONG).show();
            }

        }

    }

}
