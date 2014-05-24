package com.supinfo.cubbyhole.mobileapp.models;

import java.util.Date;

public class File {

	private int id;
	private String name;
	private Date creationDate;
	private Date lastUpdateDate;
	private String absolutePath;
	private String publicLinkPath;
    private Boolean isPublic;
    private String accessKey;
    private Double size;
	private Share share;

    public File(){}

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

    public Share getShare() {
        return share;
    }

    public void setShare(Share share) {
        this.share = share;
    }

    public File(int id, String name, Date creationDate, Date lastUpdateDate, String absolutePath, String publicLinkPath, Boolean isPublic, String accessKey, Double size, Share share) {
        this.id = id;
        this.name = name;
        this.creationDate = creationDate;
        this.lastUpdateDate = lastUpdateDate;
        this.absolutePath = absolutePath;
        this.publicLinkPath = publicLinkPath;
        this.isPublic = isPublic;
        this.accessKey = accessKey;
        this.size = size;
        this.share = share;
    }

    public Date getLastUpdateDate() {

        return lastUpdateDate;
    }

    public void setLastUpdateDate(Date lastUpdateDate) {
        this.lastUpdateDate = lastUpdateDate;
    }
}
