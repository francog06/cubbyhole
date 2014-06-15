package com.supinfo.cubbyhole.mobileapp.models;

import java.util.Date;
import java.util.List;

public class User {

	private int id;
	private String email;
	private String password;
	private Date registrationDate;
	private String userLocationIp;
	private Boolean isAdmin;
    private String error;
    private String token;

	private List<PlanHistory> listPlanHistory;
    private List<File> listFile;
    private List<Folder> listFolder;
    private List<Share> listShare;

    public User(){}

    public User(String error){
        this.id = -1;
        this.email = null;
        this.password = null;
        this.registrationDate = null;
        this.userLocationIp = null;
        this.isAdmin = null;
        this.listPlanHistory = null;
        this.listFile = null;
        this.listFolder = null;
        this.listShare = null;
        this.error = error;
        this.token = "";
    }

    public User(int id, String email, String password, Date registrationDate, String userLocationIp, Boolean isAdmin, List<PlanHistory> listPlanHistory, List<File> listFile, List<Folder> listFolder, List<Share> listShare) {
        this.id = id;
        this.email = email;
        this.password = password;
        this.registrationDate = registrationDate;
        this.userLocationIp = userLocationIp;
        this.isAdmin = isAdmin;
        this.listPlanHistory = listPlanHistory;
        this.listFile = listFile;
        this.listFolder = listFolder;
        this.listShare = listShare;
    }

    public String getToken() {
        return token;
    }

    public void setToken(String token) {
        this.token = token;
    }

    public String getError() { return error; }

    public void setError(String error) { this.error = error; }

    public String getEmail() {
		return email;
	}

	public void setEmail(String mail) {
		this.email = mail;
	}

	public String getPassword() {
		return password;
	}

	public void setPassword(String password) {
		this.password = password;
	}

	public Date getRegistrationDate() {
		return registrationDate;
	}

	public void setRegistrationDate(Date registrationDate) {
		this.registrationDate = registrationDate;
	}

	public String getUserLocationIp() {
		return userLocationIp;
	}

	public void setUserLocationIp(String userLocationIp) {
		this.userLocationIp = userLocationIp;
	}

	public int getId() {
		return id;
	}

    public void setId(int id) {
        this.id = id;
    }

    public List<PlanHistory> getListPlanHistory() {
        return listPlanHistory;
    }

    public void setListPlanHistory(List<PlanHistory> listPlanHistory) {
        this.listPlanHistory = listPlanHistory;
    }

    public List<File> getListFile() {
        return listFile;
    }

    public void setListFile(List<File> listFile) {
        this.listFile = listFile;
    }

    public List<Folder> getListFolder() {
        return listFolder;
    }

    public void setListFolder(List<Folder> listFolder) {
        this.listFolder = listFolder;
    }

    public List<Share> getListShare() {
        return listShare;
    }

    public void setListShare(List<Share> listShare) {
        this.listShare = listShare;
    }

    public Boolean getIsAdmin() {
        return isAdmin;
    }

    public void setIsAdmin(Boolean isAdmin) {
        this.isAdmin = isAdmin;
    }
}
