package com.supinfo.cubbyhole.mobileapp.activities;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;

import uk.co.senab.actionbarpulltorefresh.library.ActionBarPullToRefresh;
import uk.co.senab.actionbarpulltorefresh.library.PullToRefreshLayout;
import uk.co.senab.actionbarpulltorefresh.library.listeners.OnRefreshListener;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Typeface;
import android.location.Address;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.ContextMenu;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ContextMenu.ContextMenuInfo;
import android.widget.AdapterView;
import android.widget.AnalogClock;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.AdapterView.AdapterContextMenuInfo;

import com.supinfo.cubbyhole.mobileapp.R;
import com.supinfo.cubbyhole.mobileapp.activities.Home.AddData;
import com.supinfo.cubbyhole.mobileapp.activities.Home.DeleteData;
import com.supinfo.cubbyhole.mobileapp.adapters.ShareListAdapter;
import com.supinfo.cubbyhole.mobileapp.models.Empty;
import com.supinfo.cubbyhole.mobileapp.models.File;
import com.supinfo.cubbyhole.mobileapp.models.Folder;
import com.supinfo.cubbyhole.mobileapp.models.Share;
import com.supinfo.cubbyhole.mobileapp.utils.Data;
import com.supinfo.cubbyhole.mobileapp.utils.Utils;

public class ManagePersmissionsActivity extends ActionBarActivity implements OnRefreshListener {

	private ProgressBar pb;
	private ListView list;
	private ShareListAdapter listAdapter;
	private PullToRefreshLayout mPullToRefreshLayout;

	private Folder currentFolder = null;
	private File currentFile = null;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_permissions);

		// Recuperation de l'item selectionne
		if (Home.itemSelected instanceof File){
			currentFile = (File) Home.itemSelected;
		}else if (Home.itemSelected instanceof Folder){
			currentFolder = (Folder) Home.itemSelected;
		}else{
			Intent mIntent = new Intent(ManagePersmissionsActivity.this, DetailActivity.class);
			setResult(Utils.INTENT_MANAGEPERMISSIONS, mIntent);
			finish();
		}

		// ActionBar
		getSupportActionBar().setTitle("Partage utilisateur");
		getSupportActionBar().setDisplayHomeAsUpEnabled(true);

		SetupComponents();
		SetHandlers();

		RefreshView();

	}

	@Override
	protected void onResume() {
		super.onResume();
	}

	@Override	
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.menu_permissions, menu);
		return true;
	}

	@Override
	public void onCreateContextMenu(ContextMenu menu, View v,
			ContextMenuInfo menuInfo) {
		super.onCreateContextMenu(menu, v, menuInfo);

		AdapterContextMenuInfo info = (AdapterContextMenuInfo) menuInfo;
		MenuInflater inflater = getMenuInflater();

		inflater.inflate(R.menu.contextual_menu_permissions, menu);

	}

	@Override
	public boolean onContextItemSelected(MenuItem item) {
		AdapterContextMenuInfo info = (AdapterContextMenuInfo) item.getMenuInfo();

		Share shareSelected = null;
		
		if (list.getAdapter().getItem(info.position) instanceof Share){
			shareSelected = (Share) list.getAdapter().getItem(info.position);
		}

		switch (item.getItemId()) {

		case R.id.context_permissions_delete:
			if (shareSelected != null){
				new DeleteShare(ManagePersmissionsActivity.this, shareSelected).execute();
			}
			
			return true;
			
		}
		
		return false;
	}
	
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {

		case android.R.id.home:
			Intent mIntent = new Intent(ManagePersmissionsActivity.this, DetailActivity.class);
			setResult(Utils.INTENT_MANAGEPERMISSIONS, mIntent);
			finish();
			return true;

		case R.id.permissions_add:

			AlertDialog.Builder alert = new AlertDialog.Builder(ManagePersmissionsActivity.this);
			alert.setTitle("Ajout d'une permission");
			alert.setMessage("Merci de spécifier l'email d'un utilisateur CubbyHole :");

			final EditText input = new EditText(ManagePersmissionsActivity.this);

			final RadioGroup radioGroup = new RadioGroup(ManagePersmissionsActivity.this);
			radioGroup.setOrientation(RadioGroup.HORIZONTAL);
			RadioButton rbReadOnly = new RadioButton(ManagePersmissionsActivity.this);
			rbReadOnly.setPadding(5, 5, 5, 5);
			rbReadOnly.setText("Lecture");
			rbReadOnly.setId(1);
			rbReadOnly.setChecked(true);
			RadioButton rbReadAndWrite = new RadioButton(ManagePersmissionsActivity.this);
			rbReadAndWrite.setPadding(5, 5, 5, 5);
			rbReadAndWrite.setText("Lecture & Ecriture");
			rbReadAndWrite.setId(2);
			radioGroup.addView(rbReadOnly);           
			radioGroup.addView(rbReadAndWrite);
			radioGroup.setPadding(5, 5, 5, 5);

			LinearLayout lila1= new LinearLayout(this);
			lila1.setOrientation(1);
			lila1.addView(input);
			lila1.addView(radioGroup);
			alert.setView(lila1);

			alert.setPositiveButton("Valider", new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int whichButton) {

					String value = input.getText().toString();

					List<NameValuePair> pairs = new ArrayList<NameValuePair>();
					if (!value.trim().isEmpty() && !value.contains("!") && !value.contains("//") && !value.contains("#")){

						pairs.add(new BasicNameValuePair("email", value));

						if (currentFile != null && currentFolder == null){
							pairs.add(new BasicNameValuePair("file", String.valueOf(currentFile.getId())));
						}else if (currentFolder != null && currentFile == null){
							pairs.add(new BasicNameValuePair("folder", String.valueOf(currentFolder.getId())));
						}

						if (radioGroup.getCheckedRadioButtonId() == 1){ 		// Lecture
							pairs.add(new BasicNameValuePair("write", "0"));
						}else if (radioGroup.getCheckedRadioButtonId() == 2){	// Lecture et ecriture
							pairs.add(new BasicNameValuePair("write", "1"));
						}

						new AddShare(ManagePersmissionsActivity.this, pairs).execute();

					}else{
						Utils.DisplayToast(ManagePersmissionsActivity.this, "L'adresse mail spécifiée est invalide.");
					}

				}
			});
			alert.setNegativeButton("Annuler", new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int whichButton) {
				}
			});
			alert.show();

		default:
			return super.onOptionsItemSelected(item);
		}
	}

	@Override
	public void onRefreshStarted(View view) {
		RefreshView();
	}

	private void RefreshView(){

		if (Utils.IsNetworkAvailable(this)){
			if (currentFile != null && currentFolder == null){
				new GetShares(ManagePersmissionsActivity.this, "http://cubbyhole.name/api/file/details/"+currentFile.getId()+"/shares").execute();
			}else if (currentFolder != null && currentFile == null){
				new GetShares(ManagePersmissionsActivity.this, "http://cubbyhole.name/api/folder/details/"+currentFolder.getId()+"/shares").execute();
			}
		}else{
			Utils.DisplayToast(this, "Actualisation des données impossible car il n'y aucun réseau disponible.");
		}

	}

	private void SetupComponents(){

		pb = (ProgressBar)findViewById(R.id.permissions_pb);
		list = (ListView)findViewById(R.id.permissions_list);
		registerForContextMenu(list);
		mPullToRefreshLayout = (PullToRefreshLayout) findViewById(R.id.permissions_ptr_layout);
		// Instanciation du pulltorefresh
		ActionBarPullToRefresh.from(this)
		.allChildrenArePullable()
		.listener(this)
		.setup(mPullToRefreshLayout);

	}

	private void SetHandlers(){

		list.setOnItemClickListener(new AdapterView.OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> adapterView, View view, int position, long l) {

				final Share shareSelected = (Share) list.getAdapter().getItem(position);

				AlertDialog.Builder alert = new AlertDialog.Builder(ManagePersmissionsActivity.this);
				alert.setTitle("Permissions");

				final RadioGroup radioGroup = new RadioGroup(ManagePersmissionsActivity.this);
				radioGroup.setOrientation(RadioGroup.VERTICAL);
				RadioButton rbReadOnly = new RadioButton(ManagePersmissionsActivity.this);
				rbReadOnly.setPadding(5, 5, 5, 5);
				rbReadOnly.setText("Lecture");
				rbReadOnly.setId(1);
				RadioButton rbReadAndWrite = new RadioButton(ManagePersmissionsActivity.this);
				rbReadAndWrite.setPadding(5, 5, 5, 5);
				rbReadAndWrite.setText("Lecture & Ecriture");
				rbReadAndWrite.setId(2);
				radioGroup.addView(rbReadOnly);           
				radioGroup.addView(rbReadAndWrite);
				radioGroup.setPadding(5, 5, 5, 5);

				if (!shareSelected.getIsWritable()){
					rbReadOnly.setChecked(true);
					rbReadAndWrite.setChecked(false);
				}else{
					rbReadOnly.setChecked(false);
					rbReadAndWrite.setChecked(true);
				}

				alert.setNegativeButton("Valider", new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int whichButton) {

						List<NameValuePair> pairs = new ArrayList<NameValuePair>();
						if (radioGroup.getCheckedRadioButtonId() == 1){ 		// Lecture
							pairs.clear();
							pairs.add(new BasicNameValuePair("write", "0"));
						}else if (radioGroup.getCheckedRadioButtonId() == 2){	// Lecture et ecriture
							pairs.clear();
							pairs.add(new BasicNameValuePair("write", "1"));
						}

						new UpdateShare(ManagePersmissionsActivity.this, shareSelected, pairs).execute();

					}
				});

				alert.setView(radioGroup);
				alert.show();
			}
		});

	}

	/*
	 *  Asynctask
	 */
	
	public class DeleteShare extends AsyncTask<Void, Integer, Boolean> {

		private Context ctx;
		private Share share;
		private ProgressDialog ringProgressDialog;
		
		public DeleteShare(Context ctx, Share share) {
			this.ctx = ctx;
			this.share = share;
		}

		@Override
		protected void onPreExecute() {
			super.onPreExecute();

			ringProgressDialog = ProgressDialog.show(ctx, "Veuillez patienter...", "Suppression en cours..", true);
			ringProgressDialog.setCancelable(false);
		}

		@Override
		protected Boolean doInBackground(Void... params) {

			return Utils.DeleteShare(ctx, share.getId());
			
		}

		@Override
		protected void onPostExecute(Boolean isGood) {
			super.onPostExecute(isGood);

			if (isGood){
				ringProgressDialog.dismiss();
				RefreshView();
			}else{
				ringProgressDialog.dismiss();
				Utils.DisplayToast(ctx, Data.errorMessage);
				Data.errorMessage = getResources().getString(R.string.errorMessage);
			}

		}

	}

	public class AddShare extends AsyncTask<Void, Integer, Boolean> {

		private Context ctx;
		private List<NameValuePair> pairs;
		private ProgressDialog ringProgressDialog;

		public AddShare(Context ctx, List<NameValuePair> pairs) {
			this.ctx = ctx;
			this.pairs = pairs;
		}

		@Override
		protected void onPreExecute() {
			super.onPreExecute();

			ringProgressDialog = ProgressDialog.show(ctx, "Veuillez patienter...", "Traitement en cours..", true);
			ringProgressDialog.setCancelable(false);
		}

		@Override
		protected Boolean doInBackground(Void... params) {

			return Utils.AddShare(ctx, pairs);

		}

		@Override
		protected void onPostExecute(Boolean isGood) {
			super.onPostExecute(isGood);

			if (isGood){
				ringProgressDialog.dismiss();
				RefreshView();
			}else{
				ringProgressDialog.dismiss();
				Utils.DisplayToast(ctx, Data.errorMessage);
				Data.errorMessage = getResources().getString(R.string.errorMessage);
			}

		}

	}

	public class UpdateShare extends AsyncTask<Void, Integer, Boolean> {

		private Context ctx;
		private Share share;
		private List<NameValuePair> pairs;
		private  ProgressDialog ringProgressDialog;

		public UpdateShare(Context ctx, Share item, List<NameValuePair> pairs) {
			this.ctx = ctx;
			this.share = item;
			this.pairs = pairs;
		}

		@Override
		protected void onPreExecute() {
			super.onPreExecute();

			ringProgressDialog = ProgressDialog.show(ctx, "Veuillez patienter...", "Actualisation des permissions en cours..", true);
			ringProgressDialog.setCancelable(false);
		}

		@Override
		protected Boolean doInBackground(Void... params) {

			return Utils.UpdateShare(ctx, share, pairs);

		}

		@Override
		protected void onPostExecute(Boolean isGood) {
			super.onPostExecute(isGood);

			if (isGood){
				ringProgressDialog.dismiss();
				RefreshView();
			}else{
				ringProgressDialog.dismiss();
				Utils.DisplayToast(ctx, Data.errorMessage);
				Data.errorMessage = getResources().getString(R.string.errorMessage);
			}

		}

	}

	private class GetShares extends AsyncTask<Void, Integer, List<Share>> {

		private String url;
		private Context ctx;

		public GetShares(Context ctx, String url) {
			this.url = url;
			this.ctx = ctx;
		}

		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			pb.setVisibility(View.VISIBLE);
		}

		protected List<Share> doInBackground(Void... params) {

			try {

				return Utils.GetShares(ctx, url);

			} catch (Exception e) {
				Log.w(getClass().getSimpleName(), "exception Connect : Json");
				e.printStackTrace();
				return null;
			}
		}

		@Override
		protected void onPostExecute(List<Share> downloadedArray) {
			super.onPostExecute(downloadedArray);

			if (downloadedArray != null && downloadedArray.size() > 0){

				if (listAdapter != null){
					listAdapter.clear();
					listAdapter.notifyDataSetChanged();
				}

				List<Object> listObj = new ArrayList<Object>();
				for(Share share : downloadedArray){
					listObj.add(share); 
				}

				listAdapter = new ShareListAdapter(ManagePersmissionsActivity.this, R.layout.item_simple, listObj);
				list.setAdapter(listAdapter);

			}else{

				List<Object> emptyItemArray = new ArrayList<Object>();
				Empty emptyItem = new Empty("Aucun partage utilisateur");
				emptyItemArray.add(emptyItem);

				listAdapter = new ShareListAdapter(ManagePersmissionsActivity.this, R.layout.item_simple, emptyItemArray);
				list.setAdapter(listAdapter);

			}

			if (pb!=null){pb.setVisibility(View.GONE);}
			mPullToRefreshLayout.setRefreshComplete();
		}

	}


}
