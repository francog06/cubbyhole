package com.supinfo.cubbyhole.mobileapp.models;

import java.util.Date;

public class Share {

	private int id;
    private Date date;
	private Folder folder;
	private File file;
	private String loginOwner;
	private int ownerId;
	private String loginUser;
	private int userId;
	private Boolean isWritable;
	
	public Share() {
		
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

	public String getLoginOwner() {
		return loginOwner;
	}

	public void setLoginOwner(String loginOwner) {
		this.loginOwner = loginOwner;
	}

	public int getOwnerId() {
		return ownerId;
	}

	public void setOwnerId(int ownerId) {
		this.ownerId = ownerId;
	}

	public String getLoginUser() {
		return loginUser;
	}

	public void setLoginUser(String loginUser) {
		this.loginUser = loginUser;
	}

	public int getUserId() {
		return userId;
	}

	public void setUserId(int userId) {
		this.userId = userId;
	}

	public Boolean getIsWritable() {
		return isWritable;
	}

	public void setIsWritable(Boolean isWritable) {
		this.isWritable = isWritable;
	}
	
    
}
