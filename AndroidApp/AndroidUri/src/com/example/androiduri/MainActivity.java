package com.example.androiduri;

import java.io.File;
import java.io.FileNotFoundException;

import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.provider.MediaStore.Images;
import android.support.v4.content.CursorLoader;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.app.Activity;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;

public class MainActivity extends Activity {

	Button btnSelFile;
	TextView text1, text2, text3, note;
	ImageView image;
	
	Uri orgUri, uriFromPath;
	String convertedPath;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		btnSelFile = (Button)findViewById(R.id.selfile);
		text1 = (TextView)findViewById(R.id.text1);
		text2 = (TextView)findViewById(R.id.text2);
		text3 = (TextView)findViewById(R.id.text3);
		note = (TextView)findViewById(R.id.note);
		image = (ImageView)findViewById(R.id.image);
		
		btnSelFile.setOnClickListener(new OnClickListener(){

			@Override
			public void onClick(View arg0) {
				Intent intent = new Intent(Intent.ACTION_PICK, 
						Images.Media.EXTERNAL_CONTENT_URI);
				startActivityForResult(intent, 0);
			}});
		
		text1.setOnClickListener(new OnClickListener(){

			@Override
			public void onClick(View v) {
				image.setImageBitmap(null);
				note.setText("by Returned Uri");
				
				try {
					Bitmap bm = BitmapFactory.decodeStream(
							getContentResolver().openInputStream(orgUri));
					image.setImageBitmap(bm);	
				} catch (FileNotFoundException e) {
					e.printStackTrace();	
				}
			}});
		
		text2.setOnClickListener(new OnClickListener(){

			@Override
			public void onClick(View v) {
				image.setImageBitmap(null);
				note.setText("by Real Path");
				Bitmap bm = BitmapFactory.decodeFile(convertedPath);
				image.setImageBitmap(bm);
			}});
		
		text3.setOnClickListener(new OnClickListener(){

			@Override
			public void onClick(View v) {
				image.setImageBitmap(null);
				note.setText("by Back Uri");
				
				try {
					Bitmap bm = BitmapFactory.decodeStream(
							getContentResolver().openInputStream(uriFromPath));
					image.setImageBitmap(bm);	
				} catch (FileNotFoundException e) {
					e.printStackTrace();	
				}
			}});
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		
		if(resultCode == RESULT_OK){
			
			image.setImageBitmap(null);
			
			//Uri return from external activity
			orgUri = data.getData();
			text1.setText("Returned Uri: " + orgUri.toString() + "\n");
			
			//path converted from Uri
			convertedPath = getRealPathFromURI(orgUri);
			text2.setText("Real Path: " + convertedPath + "\n");
			
			//Uri convert back again from path
			uriFromPath = Uri.fromFile(new File(convertedPath));
			text3.setText("Back Uri: " + uriFromPath.toString() + "\n");
		}
		
	}

	public String getRealPathFromURI(Uri contentUri) {
		String[] proj = { MediaStore.Images.Media.DATA };
		
		//This method was deprecated in API level 11
		//Cursor cursor = managedQuery(contentUri, proj, null, null, null);
		
		CursorLoader cursorLoader = new CursorLoader(
		          this, 
		          contentUri, proj, null, null, null);        
		Cursor cursor = cursorLoader.loadInBackground();
		
		int column_index = 
				cursor.getColumnIndexOrThrow(MediaStore.Images.Media.DATA);
		cursor.moveToFirst();
		return cursor.getString(column_index);	
	}
	
}
