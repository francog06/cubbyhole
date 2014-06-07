package com.supinfo.cubbyhole.mobileapp.adapters;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.supinfo.cubbyhole.mobileapp.R;
import com.supinfo.cubbyhole.mobileapp.models.Back;
import com.supinfo.cubbyhole.mobileapp.models.Empty;
import com.supinfo.cubbyhole.mobileapp.models.File;
import com.supinfo.cubbyhole.mobileapp.models.Folder;
import com.supinfo.cubbyhole.mobileapp.quickactions.QuickAction;
import com.supinfo.cubbyhole.mobileapp.utils.Utils;

import java.util.List;

/**
 * Created by anthonyvialleton on 04/04/14.
 */

public class GenericListAdapter extends ArrayAdapter<Object> {

    private Context context;
    private List<Object> items;
    
    public GenericListAdapter(Context context, int resource, List<Object> items) {
        super(context, resource, items);

        this.items = items;
        this.context = context;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        /*
         *  View set
         */
        
        LayoutInflater vi = (LayoutInflater) getContext().getSystemService(
                Context.LAYOUT_INFLATER_SERVICE);

        View v = vi.inflate(R.layout.listview_item, null);
        final Object o = items.get(position);

        ImageView iconImg = (ImageView) v.findViewById(R.id.item_icon_img);
        ImageView arrow = (ImageView) v.findViewById(R.id.item_arrow);
        TextView nameTv = (TextView) v.findViewById(R.id.item_name_tv);
        TextView lastModificationDateTv = (TextView) v.findViewById(R.id.item_lastmodification_tv);
        LinearLayout ll = (LinearLayout) v.findViewById(R.id.item_ll);

        if (o != null){
        	
            if (o instanceof File){

                File file = (File)o;
                	
                arrow.setVisibility(View.GONE);
                iconImg.setImageDrawable(this.context.getResources().getDrawable(R.drawable.cubby_file));

                if (file.getName() != null){
                    nameTv.setText(file.getName());
                }

                if (file.getLastUpdateDate() != null){
                	lastModificationDateTv.setText(Utils.DateToString(file.getLastUpdateDate()));
                }
                
            }else if (o instanceof Folder){

                final Folder folder = (Folder)o;

                iconImg.setImageDrawable(this.context.getResources().getDrawable(R.drawable.cubby_folder));

                if (folder.getName() != null){
                    nameTv.setText(folder.getName());
                }

                if (folder.getLastUpdateDate() != null){
                	lastModificationDateTv.setText(Utils.DateToString(folder.getLastUpdateDate()));
                }
                

            }else if (o instanceof Back){

                Back back = (Back)o;

                arrow.setVisibility(View.GONE);
                lastModificationDateTv.setVisibility(View.GONE);

                ll.setOrientation(LinearLayout.VERTICAL);

                iconImg.setImageDrawable(this.context.getResources().getDrawable(R.drawable.arrow_up));

                LinearLayout.LayoutParams layoutParams=new LinearLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT);
                layoutParams.gravity= Gravity.CENTER;

                iconImg.setLayoutParams(layoutParams);

                if (back.getValue()!= null){
                    nameTv.setText(back.getValue());
                    nameTv.setLayoutParams(layoutParams);
                }
                
            }else if (o instanceof Empty){
            	
            	Empty empty = (Empty)o;
            	
            	arrow.setVisibility(View.GONE);
                lastModificationDateTv.setVisibility(View.GONE);

                ll.setOrientation(LinearLayout.VERTICAL);

                LinearLayout.LayoutParams layoutParams=new LinearLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT);
                layoutParams.gravity= Gravity.CENTER;

                if (empty.getValue()!= null){
                    nameTv.setText(empty.getValue());
                    nameTv.setLayoutParams(layoutParams);
                }
            	
            }
            
        }

        return v;
    }
}
