package com.supinfo.cubbyhole.mobileapp.activities;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.Menu;
import android.view.MenuItem;

import com.supinfo.cubbyhole.mobileapp.R;
import com.supinfo.cubbyhole.mobileapp.models.User;
import com.supinfo.cubbyhole.mobileapp.utils.Utils;

public class SplashscreenActivity extends Activity {

    private static final int STOPSPLASH = 0;
    private static final long SPLASHTIME = 2000;

    private final transient Handler splashHandler = new Handler() {
        @Override
        public void handleMessage(Message msg) {

            if (msg.what == STOPSPLASH) {

                User user = Utils.getUserFromSharedPreferences(SplashscreenActivity.this);

                if (user != null){

                    Intent intent_to_home = new Intent(SplashscreenActivity.this, Home.class);
                    startActivity(intent_to_home);
                    finish();

                }else{

                    Intent intent_to_login = new Intent(SplashscreenActivity.this, LoginActivity.class);
                    startActivity(intent_to_login);
                    finish();

                }

            }

            super.handleMessage(msg);
        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_splashscreen);

        final Message msg = new Message();
        msg.what = STOPSPLASH;
        splashHandler.sendMessageDelayed(msg, SPLASHTIME);
    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        
        getMenuInflater().inflate(R.menu.menu, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        if (id == R.id.action_settings) {
            return true;
        }
        return super.onOptionsItemSelected(item);
    }

}
