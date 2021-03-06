package com.supinfo.cubbyhole.mobileapp.activities;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import android.animation.Animator;
import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.pm.ActivityInfo;
import android.content.pm.PackageManager;
import android.content.pm.ResolveInfo;
import android.graphics.Typeface;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Parcelable;
import android.support.v7.app.ActionBarActivity;
import android.text.Html;
import android.util.Log;
import android.view.Gravity;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Switch;
import android.widget.TextView;

import com.supinfo.cubbyhole.mobileapp.R;
import com.supinfo.cubbyhole.mobileapp.models.File;
import com.supinfo.cubbyhole.mobileapp.models.Folder;
import com.supinfo.cubbyhole.mobileapp.utils.Utils;

public class DetailActivity extends ActionBarActivity {

	public Folder currentFolder = null;
	public File currentFile = null;

	private TextView detail_itemCreationDate_tv;
	private TextView detail_itemLastUpdateDate_tv;
	private TextView detail_itemSize_tv;
	private LinearLayout detail_size_ll;
	private Switch detail_isPublic_switch;
	private Button detail_manage_btn;
	private ImageView networkImageView;
	private Button detail_share_btn;
	private ImageView line1;
	private ImageView line2;

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
		if (currentFolder != null && currentFile == null){
			getSupportActionBar().setTitle(currentFolder.getName());
		}else if (currentFile != null && currentFolder == null){
			getSupportActionBar().setTitle(currentFile.getName());
		}
		
		getSupportActionBar().setDisplayHomeAsUpEnabled(true);

		SetupComponents();

		if (currentFolder != null){
			SetupFolder();
		}else if (currentFile != null){
			SetupFile();
		}else{
			finish();
		}

		if (currentFile != null){
			SetupFile();
		}else if (currentFolder != null){
			SetupFolder();
		}else{
			finish();
		}
	}

	private void SetupComponents(){

		networkImageView = (ImageView) findViewById(R.id.detail_networkImageView);
		detail_itemCreationDate_tv = (TextView) findViewById(R.id.detail_itemCreationDate_tv);
		detail_itemLastUpdateDate_tv = (TextView) findViewById(R.id.detail_itemLastUpdateDate_tv);
		detail_size_ll = (LinearLayout) findViewById(R.id.detail_sizell);
		detail_itemSize_tv = (TextView) findViewById(R.id.detail_itemSize_tv);
		detail_manage_btn = (Button) findViewById(R.id.detail_manage_btn);
		detail_isPublic_switch = (Switch) findViewById(R.id.detail_isPublic_switch);
		detail_share_btn = (Button) findViewById(R.id.detail_share_btn);
		line1 = (ImageView) findViewById(R.id.detail_line1);
		line2 = (ImageView) findViewById(R.id.detail_line2);

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

	private void shareContent(String sharingMessage) {

		Intent intent = new Intent(android.content.Intent.ACTION_SEND);
		intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_WHEN_TASK_RESET);
		intent.setType("text/plain");
		intent.putExtra(Intent.EXTRA_TEXT, sharingMessage);
		startActivity(Intent.createChooser(intent, "Partager ce fichier"));
	}

	/*
	 *  Folder
	 */

	private void SetupFolder(){

		networkImageView.setImageDrawable(getResources().getDrawable(R.drawable.wc_folder_blue));
		detail_isPublic_switch.setVisibility(View.GONE);
		detail_share_btn.setVisibility(View.GONE);
		detail_size_ll.setVisibility(View.GONE);
		line1.setVisibility(View.GONE);
		line2.setVisibility(View.GONE);

		// Date de creation
		detail_itemCreationDate_tv.setText(Utils.DateToString(currentFolder.getCreationDate()));

		// Date de maj
		detail_itemLastUpdateDate_tv.setText(Utils.DateToString(currentFolder.getLastUpdateDate()));

		// Manage
		detail_manage_btn.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {

				Intent intent_to_managepermissions = new Intent(DetailActivity.this, ManagePersmissionsActivity.class);
				startActivityForResult(intent_to_managepermissions, Utils.INTENT_MANAGEPERMISSIONS);

			}
		});

	}


	/*
	 *  File
	 */

	private void SetupFile(){

		networkImageView.setImageDrawable(getResources().getDrawable(R.drawable.cubby_file));
		// Preview image
		/*if (currentFile.getName().contains(".jpg") || currentFile.getName().contains(".jpeg") || 
				currentFile.getName().contains(".png") || currentFile.getName().contains(".jfif")){

			String url = Utils.FILE+"details/"+currentFile.getId()+"/preview?hash="+Utils.HASH_DL;
			// Asynctask image
			networkImageView.setImageDrawable(getResources().getDrawable(R.drawable.wc_file_blue));
		}*/
		
		networkImageView.setImageDrawable(getResources().getDrawable(R.drawable.wc_file_blue));
		
		// Date de creation
		detail_itemCreationDate_tv.setText(Utils.DateToString(currentFile.getCreationDate()));

		// Date de maj
		detail_itemLastUpdateDate_tv.setText(Utils.DateToString(currentFile.getLastUpdateDate()));

		// Taille
		detail_itemSize_tv.setText(currentFile.getSize().toString()+" Mo");

		// Publique
		detail_isPublic_switch.setChecked(currentFile.getIsPublic());
		if (!currentFile.getIsPublic()){
			detail_share_btn.setVisibility(View.GONE);
		}else{
			detail_share_btn.setVisibility(View.VISIBLE);
		}

		detail_isPublic_switch.setOnCheckedChangeListener(new OnCheckedChangeListener() {
			@Override
			public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {

				if (isChecked){

					// Asynctask updade
					new UpdateFile(DetailActivity.this, currentFile, true).execute();

				}else if (!isChecked){

					// Asynctask update
					new UpdateFile(DetailActivity.this, currentFile, false).execute();

				}
			}
		});

		// Partage
		detail_share_btn.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				shareContent("°° CubbyHole Information °°\n "+Utils.getUserFromSharedPreferences(DetailActivity.this).getEmail()+" souhaite partager un "
						+ "fichier avec vous! Ce fichier est téléchargeable via l'url suivante : "+Utils.FILE+"download/"+currentFile.getId()+"?accessKey="+currentFile.getAccessKey());
			}
		});

		// Gerer -> liste d'User avec permissions
		detail_manage_btn.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {

				Intent intent_to_managepermissions = new Intent(DetailActivity.this, ManagePersmissionsActivity.class);
				startActivityForResult(intent_to_managepermissions, Utils.INTENT_MANAGEPERMISSIONS);

			}
		});

	}

	/*
	 *  Asynctask
	 */

	public class UpdateFile extends AsyncTask<Void, Integer, File> {

		private Context ctx;
		private File file;
		private ProgressDialog ringProgressDialog;
		private Boolean isPublic;

		public UpdateFile(Context ctx, File file, Boolean isPublic) {
			this.ctx = ctx;
			this.file = file;
			this.isPublic = isPublic;
		}

		@Override
		protected void onPreExecute() {
			super.onPreExecute();

			ringProgressDialog = ProgressDialog.show(ctx, "Veuillez patienter...", "Mise à jour en cours..", true);
			ringProgressDialog.setCancelable(false);
		}

		@Override
		protected File doInBackground(Void... params) {

			List<NameValuePair> pairs = new ArrayList<NameValuePair>();

			String strPublic = isPublic ? "1" : "0";
			pairs.add(new BasicNameValuePair(Utils.JSON_FOLDER_ISPUBLIC, strPublic));
			return Utils.UpdateFile(ctx, file, pairs);

		}

		@Override
		protected void onPostExecute(File file) {
			super.onPostExecute(file);

			ringProgressDialog.dismiss();

			if (file != null){
				currentFile = file;
				SetupFile();
				String message = currentFile.getIsPublic() ? "Le fichier est désormais publique et vous pouvez ainsi le partager à n'importe qui!" : "Le fichier n'est plus publique et son accès libre a ete supprimé.";
				Utils.DisplayToast(ctx, message);
			}else{
				SetupFile();
				Utils.DisplayToast(ctx, "La mise à jour du fichier a échouée. Veuillez réessayer ultéireurement.");
			}

		}
	}


}