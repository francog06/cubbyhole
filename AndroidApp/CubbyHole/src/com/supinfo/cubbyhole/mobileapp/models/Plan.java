package com.supinfo.cubbyhole.mobileapp.models;

import java.util.List;

public class Plan {

	private int id;
	private String planName;
	private double price;
	private double duration;
	private double usableStorageSpace;
	private double maxBandwidth;
	private double dailyDataTransfert;

	private List<PlanHistory> listPlanHistory;

    public Plan(int id, String planName, double price, double duration, double usableStorageSpace, double maxBandwidth, double dailyDataTransfert, List<PlanHistory> listPlanHistory) {
        this.id = id;
        this.planName = planName;
        this.price = price;
        this.duration = duration;
        this.usableStorageSpace = usableStorageSpace;
        this.maxBandwidth = maxBandwidth;
        this.dailyDataTransfert = dailyDataTransfert;
        this.listPlanHistory = listPlanHistory;
    }

    public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public String getPlanName() {
		return planName;
	}

	public void setPlanName(String planName) {
		this.planName = planName;
	}

	public double getPrice() {
		return price;
	}

	public void setPrice(double price) {
		this.price = price;
	}

	public double getDuration() {
		return duration;
	}

	public void setDuration(double duration) {
		this.duration = duration;
	}

	public double getUsableStorageSpace() {
		return usableStorageSpace;
	}

	public void setUsableStorageSpace(double usableStorageSpace) {
		this.usableStorageSpace = usableStorageSpace;
	}

	public double getMaxBandwidth() {
		return maxBandwidth;
	}

	public void setMaxBandwidth(double maxBandwidth) {
		this.maxBandwidth = maxBandwidth;
	}

	public double getDailyDataTransfert() {
		return dailyDataTransfert;
	}

	public void setDailyDataTransfert(double dailyDataTransfert) {
		this.dailyDataTransfert = dailyDataTransfert;
	}

    public List<PlanHistory> getListPlanHistory() {
        return listPlanHistory;
    }

    public void setListPlanHistory(List<PlanHistory> listPlanHistory) {
        this.listPlanHistory = listPlanHistory;
    }


}
