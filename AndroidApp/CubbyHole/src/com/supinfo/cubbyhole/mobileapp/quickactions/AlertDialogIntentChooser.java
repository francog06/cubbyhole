package com.supinfo.cubbyhole.mobileapp.quickactions;

import java.util.List;

import com.supinfo.cubbyhole.mobileapp.R;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.ActivityNotFoundException;
import android.content.DialogInterface;
import android.content.DialogInterface.OnCancelListener;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.pm.ResolveInfo;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.MimeTypeMap;
import android.widget.ArrayAdapter;
import android.widget.ListAdapter;
import android.widget.TextView;
import android.widget.Toast;

public class AlertDialogIntentChooser {
private String filePath;
private Activity activity;
private AlertDialog dialog;
private AlertDialogDelegate delegate;
private ListItem[] items;

public AlertDialogIntentChooser(String filePath,Activity activity){
    this.filePath = filePath;
    this.activity = activity;
    init();
}

public void setDialogDelegate(AlertDialogDelegate delegate){
    this.delegate = delegate;
}

private void init(){

    initApplicationItems();

    AlertDialog.Builder builder = new AlertDialog.Builder(activity);
    builder.setTitle("Veuillez selectionner une application");
    builder.setIcon(R.drawable.ic_action_cloud);

    builder.setOnCancelListener(new OnCancelListener() {

        @Override
        public void onCancel(DialogInterface paramDialogInterface) {
            if(delegate!=null)
                delegate.onDialogCancelled(paramDialogInterface);
        }
    });

    builder.setAdapter(adapter, new DialogInterface.OnClickListener() {
        @Override
        public void onClick(DialogInterface dialog, int which) {         

            Intent intentPdf = new Intent(Intent.ACTION_VIEW);
            MimeTypeMap myMime = MimeTypeMap.getSingleton();
            String fileExt = MimeTypeMap.getFileExtensionFromUrl(filePath);
            String mimeType = myMime.getMimeTypeFromExtension(fileExt);                 
            intentPdf.setClassName(items[which].context, items[which].packageClassName);
            intentPdf.setDataAndType(Uri.parse(filePath), mimeType);
            try {
                activity.startActivity(intentPdf);
                dialog.dismiss();
                if(delegate!=null)
                    delegate.onItemSelected(items[which].context, items[which].packageClassName);
            }catch (ActivityNotFoundException e) {
                Toast.makeText(activity, 
                        "Aucune application", 
                        Toast.LENGTH_SHORT).show();
                dialog.dismiss();
            }               
        }
    });

    dialog = builder.create();        
}

private void initApplicationItems(){
    Intent intentPdf = new Intent(Intent.ACTION_VIEW);

    MimeTypeMap myMime = MimeTypeMap.getSingleton();
    String fileExt = MimeTypeMap.getFileExtensionFromUrl(filePath);
    String mimeType = myMime.getMimeTypeFromExtension(fileExt);             
    intentPdf.setDataAndType(Uri.parse(filePath), mimeType);
    PackageManager pm = activity.getPackageManager();
    List<ResolveInfo> resInfos = pm.queryIntentActivities(intentPdf, 0);

    items = new ListItem[resInfos.size()];
    int i = 0;
    for (ResolveInfo resInfo : resInfos) {
        String context = resInfo.activityInfo.packageName;
        String packageClassName = resInfo.activityInfo.name;
        CharSequence label = resInfo.loadLabel(pm);
        Drawable icon = resInfo.loadIcon(pm);
        items[i] = new ListItem(label.toString(), icon, context, packageClassName);
        ++i;
    }
}

public void show(){
    dialog.show();
}

private ListAdapter adapter = new ArrayAdapter<ListItem>(
          activity,
    android.R.layout.select_dialog_item,
    android.R.id.text1,
    items){

    public View getView(int position, View convertView, ViewGroup parent) {

        View v = super.getView(position, convertView, parent);
        TextView tv = (TextView)v.findViewById(android.R.id.text1);

        int dpS = (int) (72 * activity.getResources().getDisplayMetrics().density *  0.5f);
        items[position].icon.setBounds(0, 0, dpS, dpS);
        tv.setCompoundDrawables(items[position].icon, null, null, null);

        int dp5 = (int) (5 * activity.getResources().getDisplayMetrics().density *  0.5f);
        tv.setCompoundDrawablePadding(dp5);

        return v;
    }
};

class ListItem {
     public final String name;
     public final Drawable icon;
     public final String context;
     public final String packageClassName;

     public ListItem(String text, Drawable icon, String context, String packageClassName) {
         this.name = text;
         this.icon = icon;
         this.context = context;
         this.packageClassName = packageClassName;
     }

     @Override
     public String toString() {
         return name;
     }
 }

 public static interface AlertDialogDelegate{
     public void onDialogCancelled(DialogInterface paramDialogInterface);
     public void onItemSelected(String packageName, String className);
 }
}