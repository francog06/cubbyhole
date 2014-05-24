package com.supinfo.cubbyhole.mobileapp.models;

import java.util.Date;

/**
 * Created by anthonyvialleton on 03/04/14.
 */

public class DataHistory {

    private int id;
    private Date date;
    private String ip;
    private String country;

    private File file;

    public DataHistory(){}

    public DataHistory(int id, Date date, String ip, String country, File file) {
        this.id = id;
        this.date = date;
        this.ip = ip;
        this.country = country;
        this.file = file;
    }

    public File getFile() {
        return file;
    }

    public void setFile(File file) {
        this.file = file;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    public String getIp() {
        return ip;
    }

    public void setIp(String ip) {
        this.ip = ip;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }


}
