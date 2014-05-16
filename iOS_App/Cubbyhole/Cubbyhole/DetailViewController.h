//
//  DetailViewController.h
//  Cubbyhole
//
//  Created by Mathieu MORICEAU on 15/05/14.
//  Copyright (c) 2014 Cubbyhole Staff. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SBJsonParser.h";
#import "SVProgressHUD.h"

@interface DetailViewController : UIViewController <UISplitViewControllerDelegate>
@property (weak, nonatomic) IBOutlet UIBarButtonItem *trashButton;
@property (weak, nonatomic) IBOutlet UIBarButtonItem *actionButton;
@property (weak, nonatomic) IBOutlet UIImageView *imagePreview;

- (IBAction)actionButtonClicked:(id)sender;
- (IBAction)deleteButtonClicked:(id)sender;
- (void)loadImage;

@property (strong, nonatomic) id detailItem;

@property (weak, nonatomic) IBOutlet UILabel *detailDescriptionLabel;
@end
