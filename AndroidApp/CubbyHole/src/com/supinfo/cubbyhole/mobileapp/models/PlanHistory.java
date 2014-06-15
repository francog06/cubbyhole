package com.supinfo.cubbyhole.mobileapp.models;

import java.util.Date;

public class PlanHistory {

	private int id;
	private Date subscriptionPlanDate;
	private Date expirationPlanDate;
	private Boolean isActive;
	
	private User user;
	private Plan plan;

    public PlanHistory(int id, Date subscriptionPlanDate, Date expirationPlanDate, Boolean isActive, User user, Plan plan) {
        this.id = id;
        this.subscriptionPlanDate = subscriptionPlanDate;
        this.expirationPlanDate = expirationPlanDate;
        this.isActive = isActive;
        this.user = user;
        this.plan = plan;
    }

    public int getId() {
		return id;
	}


	public void setId(int id) {
		this.id = id;
	}


	public Date getSubscriptionPlanDate() {
		return subscriptionPlanDate;
	}


	public void setSubscriptionPlanDate(Date subscriptionPlanDate) {
		this.subscriptionPlanDate = subscriptionPlanDate;
	}


	public Date getExpirationPlanDate() {
		return expirationPlanDate;
	}


	public void setExpirationPlanDate(Date expirationPlanDate) {
		this.expirationPlanDate = expirationPlanDate;
	}

	public Boolean getIsActive() {
		return isActive;
	}


	public void setIsActive(Boolean isActive) {
		this.isActive = isActive;
	}

    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }

    public Plan getPlan() {
        return plan;
    }

    public void setPlan(Plan plan) {
        this.plan = plan;
    }


}
