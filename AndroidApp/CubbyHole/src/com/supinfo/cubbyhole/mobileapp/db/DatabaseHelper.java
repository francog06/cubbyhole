package com.supinfo.cubbyhole.mobileapp.db;

import android.content.Context;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.util.Log;

import com.j256.ormlite.android.apptools.OrmLiteSqliteOpenHelper;
import com.j256.ormlite.dao.Dao;
import com.j256.ormlite.support.ConnectionSource;
import com.j256.ormlite.table.TableUtils;
import com.supinfo.cubbyhole.mobileapp.models.DataHistory;
import com.supinfo.cubbyhole.mobileapp.models.File;
import com.supinfo.cubbyhole.mobileapp.models.Folder;
import com.supinfo.cubbyhole.mobileapp.models.Plan;
import com.supinfo.cubbyhole.mobileapp.models.PlanHistory;
import com.supinfo.cubbyhole.mobileapp.models.Share;
import com.supinfo.cubbyhole.mobileapp.models.User;

import java.util.ArrayList;
import java.util.List;


public class DatabaseHelper extends OrmLiteSqliteOpenHelper {
	// name of the database file for your application
	private static final String DATABASE_NAME = "Cubbyhole.sqlite";

	// any time you make changes to your database objects, you may have to
	// increase the database version
	private static final int DATABASE_VERSION = 1;

	// the DAO object we use to access the SimpleData table
	private Dao<File, Integer> fileDao = null;
	private Dao<Folder, Integer> folderDao = null;
	private Dao<Share, Integer> shareDao = null;
	private Dao<Plan, Integer> planDao = null;
	private Dao<PlanHistory, Integer> planHistoryDao = null;
	private Dao<User, Integer> userDao = null;
    private Dao<DataHistory, Integer> dataHistoryDao = null;

	public DatabaseHelper(Context context) {
		super(context, DATABASE_NAME, null, DATABASE_VERSION);
		//super(context, Environment.getExternalStorageDirectory().getAbsolutePath()
			//	    + File.separator + DATABASE_NAME, null, DATABASE_VERSION);
	}

	@Override
	public void onCreate(SQLiteDatabase database, ConnectionSource connectionSource) {
		try {
			TableUtils.createTable(connectionSource, File.class);
			TableUtils.createTable(connectionSource, Folder.class);
			TableUtils.createTable(connectionSource, Share.class);
			TableUtils.createTable(connectionSource, Plan.class);
			TableUtils.createTable(connectionSource, PlanHistory.class);
			TableUtils.createTable(connectionSource, User.class);

		} catch (SQLException e) {
			Log.e(DatabaseHelper.class.getName(), "Can't create database", e);
			throw new RuntimeException(e);
		} catch (java.sql.SQLException e) {
			e.printStackTrace();
		}

	}

	@Override
	public void onUpgrade(SQLiteDatabase db, ConnectionSource connectionSource,
			int oldVersion, int newVersion) {
		try {
			List<String> allSql = new ArrayList<String>();
			switch (oldVersion) {
			case 1:
				// allSql.add("alter table AdData add column `new_col` VARCHAR");
				// allSql.add("alter table AdData add column `new_col2` VARCHAR");
			}
			for (String sql : allSql) {
				db.execSQL(sql);
			}
		} catch (SQLException e) {
			Log.e(DatabaseHelper.class.getName(), "exception during onUpgrade",
					e);
			throw new RuntimeException(e);
		}
	}

	public Dao<File, Integer> getFileDao() {
		if (fileDao == null) {
			try {
				fileDao = getDao(File.class);
			} catch (java.sql.SQLException e) {
				e.printStackTrace();
			}
		}
		return fileDao;
	}
	
	public Dao<Folder, Integer> getFolderDao(){
		if (folderDao == null){
			try {
				folderDao = getDao(Folder.class);
			} catch (java.sql.SQLException e) {
				e.printStackTrace();
			}
		}
		return folderDao;
	}

	public Dao<Share, Integer> getShareDao() {
		if (shareDao == null) {
			try {
                shareDao = getDao(Share.class);
			} catch (java.sql.SQLException e) {
				e.printStackTrace();
			}
		}
		return shareDao;
	}

	public Dao<Plan, Integer> getPlanDao() {
		if (planDao == null) {
			try {
				planDao = getDao(Plan.class);
			} catch (java.sql.SQLException e) {
				e.printStackTrace();
			}
		}
		return planDao;
	}

	public Dao<PlanHistory, Integer> getPlanHistoryDao() {
		if (planHistoryDao == null) {
			try {
				planHistoryDao = getDao(PlanHistory.class);
			} catch (java.sql.SQLException e) {
				e.printStackTrace();
			}
		}
		return planHistoryDao;
	}

	public Dao<User, Integer> getUserDao() {
		if (userDao == null) {
			try {
				userDao = getDao(User.class);
			} catch (java.sql.SQLException e) {
				e.printStackTrace();
			}
		}
		return userDao;
	}
	
	public Dao<DataHistory, Integer> getDataHistoryDao() {
		if (dataHistoryDao == null) {
			try {
                dataHistoryDao = getDao(DataHistory.class);
			} catch (java.sql.SQLException e) {
				e.printStackTrace();
			}
		}
		return dataHistoryDao;
	}

}
