package com.supinfo.cubbyhole.mobileapp.models;

import java.util.List;
import java.util.Date;

public class Share {

	private int id;
    private Date date;

    private List<User> listUser;
	private Folder folder;
	private File file;

    public Share(int id, Date date, List<User> listUser, Folder folder, File file) {
        this.id = id;
        this.date = date;
        this.listUser = listUser;
        this.folder = folder;
        this.file = file;
    }

    public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

    public Folder getFolder() {
        return folder;
    }

    public void setFolder(Folder folder) {
        this.folder = folder;
    }

    public File getFile() {
        return file;
    }

    public void setFile(File file) {
        this.file = file;
    }

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    public List<User> getListUser() {
        return listUser;
    }

    public void setListUser(List<User> listUser) {
        this.listUser = listUser;
    }
}