package com.supinfo.cubbyhole.mobileapp.adapters;

import java.util.ArrayList;
import java.util.List;

import com.supinfo.cubbyhole.mobileapp.models.Folder;
import com.supinfo.cubbyhole.mobileapp.utils.Utils;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.supinfo.cubbyhole.mobileapp.R;

public class DrawerListAdapter extends ArrayAdapter<String> {

    private Context context;

    public DrawerListAdapter(Context context, int textViewResourceId, ArrayList<String> items) {
        super(context, textViewResourceId, items);
        
        this.context = context;
    
    }

    public View getView(int position, View convertView, ViewGroup parent) {
    	
        View view = convertView;
        if (view == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            view = inflater.inflate(R.layout.drawer_list_item, null);
        }

        String item = getItem(position);
        if (item!= null) {
            TextView itemView = (TextView) view.findViewById(R.id.drawer_listitem_tv);
            if (itemView != null) {
                itemView.setText(item);
            }
         }
        
        ImageView icone = (ImageView) view.findViewById(R.id.drawer_listitem_img);
        if (position == Utils.DRAWER_HOME_SELECTED){
        	icone.setImageResource(R.drawable.a_pictures);
        }else if (position == Utils.DRAWER_SHARES_SELECTED){
        	icone.setImageResource(R.drawable.a_about);
        }

        return view;
    }
}