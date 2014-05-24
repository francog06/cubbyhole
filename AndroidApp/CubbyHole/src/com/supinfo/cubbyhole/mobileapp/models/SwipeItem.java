package com.supinfo.cubbyhole.mobileapp.models;

/**
 * Created by anvialle on 4/17/2014.
 */
import android.graphics.drawable.Drawable;

public class SwipeItem {

    String itemName;
    Drawable icon;

    public SwipeItem(String itemName, Drawable icon) {
        super();
        this.itemName = itemName;
        this.icon = icon;
    }
    public String getItemName() {
        return itemName;
    }
    public void setItemName(String itemName) {
        this.itemName = itemName;
    }
    public Drawable getIcon() {
        return icon;
    }
    public void setIcon(Drawable icon) {
        this.icon = icon;
    }

}
