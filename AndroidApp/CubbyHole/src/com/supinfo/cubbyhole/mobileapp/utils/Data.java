package com.supinfo.cubbyhole.mobileapp.utils;

import java.util.List;
import com.supinfo.cubbyhole.mobileapp.models.Folder;

/**
 * Created by anthonyvialleton on 04/04/14.
 */

public class Data {
    public static Folder currentFolder = null;
    public static List<Object> currentArray = null;
    public static String errorMessage = "Une erreur est survenue. Veuillez vérifier vos droits d'accès ainsi que votre connexion internet et réessayez ultérieurement.";
    public static int currentDrawerSelected = 0;
}
