package com.supinfo.cubbyhole.mobileapp.models;

import java.io.Serializable;
import java.util.Date;

public class Folder implements Serializable {

	private int id;
	private String name;
	private Date creationDate;
    private Date lastUpdateDate;
    private Boolean isPublic;
    private String accessKey;
    private int parentID;
	private Share share;

    public Folder(){}

    public Folder(int id, String name, Date creationDate, Date lastUpdateDate, Boolean isPublic, String accessKey, int parentID, Share share) {
        this.id = id;
        this.name = name;
        this.creationDate = creationDate;
        this.lastUpdateDate = lastUpdateDate;
        this.isPublic = isPublic;
        this.accessKey = accessKey;
        this.parentID = parentID;
        this.share = share;
    }

    public int getId() {
		return id;
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

    public Share getShare() {
        return share;
    }

    public void setShare(Share share) {
        this.share = share;
    }

    public Date getLastUpdateDate() {
        return lastUpdateDate;
    }

    public void setLastUpdateDate(Date lastUpdateDate) {
        this.lastUpdateDate = lastUpdateDate;
    }
}
