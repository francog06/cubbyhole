package com.supinfo.cubbyhole.mobileapp.models;

/**
 * Created by anthonyvialleton on 16/05/14.
 */
public class Back {

    private String value;
    private String backUrl;

    public Back() {
    }

    public Back(String value) {
        this.value = value;
    }

    public Back(String value, String backUrl) {
        this.value = value;
        this.backUrl = backUrl;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }

    public String getBackUrl() {
        return backUrl;
    }

    public void setBackUrl(String backUrl) {
        this.backUrl = backUrl;
    }
}
