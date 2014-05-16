//
//  LoginViewController.h
//  Cubbyhole
//
//  Created by Mathieu MORICEAU on 15/05/14.
//  Copyright (c) 2014 Cubbyhole Staff. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SBJson.h"
#import "SVProgressHUD.h"

@interface LoginViewController : UIViewController
@property (weak, nonatomic) IBOutlet UITextField *txtEmail;
@property (weak, nonatomic) IBOutlet UITextField *txtPassword;
- (IBAction)loginClicked:(id)sender;
- (IBAction)registerClicked:(id)sender;
- (IBAction)backgroundClick:(id)sender;
- (void)logUser:(NSString *)email user_password:(NSString *)password;

@end
