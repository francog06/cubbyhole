//
//  MasterViewController.h
//  Cubbyhole
//
//  Created by Mathieu MORICEAU on 15/05/14.
//  Copyright (c) 2014 Cubbyhole Staff. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SBJsonParser.h"
#import "SVProgressHUD.h"

@class DetailViewController;

@interface MasterViewController : UITableViewController

@property NSString *folder_id;
@property UIBarButtonItem *backButton;
@property NSDictionary *current_folder;
@property (strong, nonatomic) DetailViewController *detailViewController;

@end
