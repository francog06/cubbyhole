package com.supinfo.cubbyhole.mobileapp.models;

import java.io.Serializable;
import java.util.Date;

public class File implements Serializable{

	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	private int id;
	private String name;
	private Date creationDate;
	private Date lastUpdateDate;
	private String absolutePath;
	private String publicLinkPath;
    private Boolean isPublic;
    private String accessKey;
    private Double size;
    
    private Boolean isFromShared = false;
    private Boolean isWritable = false;
    private int idShare = -1;
    
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

	public String getAbsolutePath() {
		return absolutePath;
	}

	public void setAbsolutePath(String absolutePath) {
		this.absolutePath = absolutePath;
	}

	public String getPublicLinkPath() {
		return publicLinkPath;
	}

	public void setPublicLinkPath(String publicLinkPath) {
		this.publicLinkPath = publicLinkPath;
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

    public Double getSize() {
        return size;
    }

    public void setSize(Double size) {
        this.size = size;
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
