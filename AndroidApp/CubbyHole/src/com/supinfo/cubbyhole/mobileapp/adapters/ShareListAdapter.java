package com.supinfo.cubbyhole.mobileapp.adapters;

import java.util.List;

import com.supinfo.cubbyhole.mobileapp.models.Empty;
import com.supinfo.cubbyhole.mobileapp.models.Folder;
import com.supinfo.cubbyhole.mobileapp.models.Share;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.supinfo.cubbyhole.mobileapp.R;

public class ShareListAdapter extends ArrayAdapter<Object> {

    private Context context;

    public ShareListAdapter(Context context, int textViewResourceId, List<Object> shares) {
        super(context, textViewResourceId, shares);
        
        this.context = context;
    
    }

    public View getView(int position, View convertView, ViewGroup parent) {
    	
        View view = convertView;
        if (view == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            view = inflater.inflate(R.layout.item_simple, null);
        }

        Object item = getItem(position);
        if (item!= null) {
        	
        	if (item instanceof Share){
        		
        		Share share = (Share) item;
        		
        		TextView itemView = (TextView) view.findViewById(R.id.move_tv);
                if (itemView != null) {
                    itemView.setText(share.getLoginUser());
                }
                
        	}else if (item instanceof Empty){
        		
        		Empty empty = (Empty) item;
        		
        		TextView itemView = (TextView) view.findViewById(R.id.move_tv);
                if (itemView != null) {
                    itemView.setText(empty.getValue());
                }
        		
        	}
         }

        return view;
    }
}