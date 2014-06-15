package com.supinfo.cubbyhole.mobileapp.models;

import java.io.Serializable;
import java.util.Date;

public class Folder implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	private int id;
	private String name;
	private Date creationDate;
    private Date lastUpdateDate;
    private Boolean isPublic = false;
    private String accessKey;
    private int parentID;
    
    private Boolean isFromShared = false;
    private Boolean isWritable = false;
    private int 	idShare = -1;
    
    public Folder(){}

    public int getId() {
		return this.id;
	}
    
	public void setId(int id) {
		this.id = id;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public Date getCreationDate() {
		return creationDate;
	}

	public void setCreationDate(Date creationDate) {
		this.creationDate = creationDate;
	}

    public Boolean getIsPublic() {
        return isPublic;
    }

    public void setIsPublic(Boolean isPublic) {
        this.isPublic = isPublic;
    }

    public String getAccessKey() {
        return accessKey;
    }

    public void setAccessKey(String accessKey) {
        this.accessKey = accessKey;
    }

    public int getParentID() {
        return parentID;
    }

    public void setParentID(int parentID) {
        this.parentID = parentID;
    }

    public Date getLastUpdateDate() {
        return lastUpdateDate;
    }

    public void setLastUpdateDate(Date lastUpdateDate) {
        this.lastUpdateDate = lastUpdateDate;
    }

	public Boolean getIsFromShared() {
		return isFromShared;
	}

	public void setIsFromShared(Boolean isFromShared) {
		this.isFromShared = isFromShared;
	}

	public Boolean getIsWritable() {
		return isWritable;
	}

	public void setIsWritable(Boolean isWritable) {
		this.isWritable = isWritable;
	}

	public int getIdShare() {
		return idShare;
	}

	public void setIdShare(int idShare) {
		this.idShare = idShare;
	}
    
    
}
