package com.supinfo.cubbyhole.mobileapp.adapters;

import java.util.List;

import com.supinfo.cubbyhole.mobileapp.models.Folder;
import com.supinfo.cubbyhole.mobileapp.utils.Data;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.supinfo.cubbyhole.mobileapp.R;

public class MoveListAdapter extends ArrayAdapter<Folder> {

    private Context context;

    public MoveListAdapter(Context context, int textViewResourceId, List<Folder> folders) {
        super(context, textViewResourceId, folders);
        
        this.context = context;
    
    }

    public View getView(int position, View convertView, ViewGroup parent) {
    	
        View view = convertView;
        if (view == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            view = inflater.inflate(R.layout.item_list_move, null);
        }

        Folder item = getItem(position);
        if (item!= null) {
            TextView itemView = (TextView) view.findViewById(R.id.list_move_tv);
            if (itemView != null) {
                itemView.setText(item.getName());
            }
            
            ImageView imageView = (ImageView) view.findViewById(R.id.list_move_img);
            if (imageView != null){
            	
            	if (item.getIsFromShared()){ // Dossier partagé
            		imageView.setImageDrawable(context.getResources().getDrawable(R.drawable.wc_folder_shared));
            	}else if (Data.currentFolder != null && item.getId() == Data.currentFolder.getParentID()){ // Dossier précédent
            		imageView.setImageDrawable(context.getResources().getDrawable(R.drawable.wc_return));
            	}else{
            		imageView.setImageDrawable(context.getResources().getDrawable(R.drawable.wc_folder_blue));
            	}
            	
            }
            
         }

        return view;
    }
}