package com.supinfo.cubbyhole.mobileapp.activities;

import com.supinfo.cubbyhole.mobileapp.R;
import com.supinfo.cubbyhole.mobileapp.models.File;
import com.supinfo.cubbyhole.mobileapp.models.Folder;
import com.supinfo.cubbyhole.mobileapp.utils.Utils;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.view.MenuItem;

public class DetailActivity extends ActionBarActivity {

	Folder currentFolder = null;
	File currentFile = null;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_detail);
		
		// Recuperation de l'item selectionne
		if (Home.itemSelected instanceof File){
			currentFile = (File) Home.itemSelected;
		}else if (Home.itemSelected instanceof Folder){
			currentFolder = (Folder) Home.itemSelected;
		}else{
			 Intent mIntent = new Intent(DetailActivity.this, Home.class);
			 setResult(Utils.INTENT_DETAIL, mIntent);
			 finish();
		}
		
		// ActionBar
		if (currentFolder != null){
			getSupportActionBar().setTitle(currentFolder.getName());
		}else{
			getSupportActionBar().setTitle(currentFile.getName());
		}
		 getSupportActionBar().setDisplayHomeAsUpEnabled(true);
		
		if (currentFolder != null){
			SetupFolder();
		}else if (currentFile != null){
			SetupFile();
		}
		 
	}
	
	@Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
        case android.R.id.home:
        	 Intent mIntent = new Intent(DetailActivity.this, Home.class);
			 setResult(Utils.INTENT_DETAIL, mIntent);
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
	
	
	
	/*
	 *  Folder
	 */
	
	private void SetupFolder(){
		
		
		
	}
	
	
	
	
	/*
	 *  Folder
	 */
	
	private void SetupFile(){
		
		
		
	}
	
}
