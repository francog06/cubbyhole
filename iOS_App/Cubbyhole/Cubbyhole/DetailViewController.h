//
//  DetailViewController.h
//  Cubbyhole
//
//  Created by Mathieu MORICEAU on 15/05/14.
//  Copyright (c) 2014 Cubbyhole Staff. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SBJsonParser.h"
#import "SVProgressHUD.h"

#import <AvailabilityMacros.h>

extern NSString * const SVPProgressLoadImage;
extern NSString * const SVPProgressSetPublic;
extern NSString * const SVPProgressDeleteFile;

@interface DetailViewController : UIViewController <UISplitViewControllerDelegate>

@property (weak, nonatomic) IBOutlet UIScrollView *scrollView;
@property (weak, nonatomic) IBOutlet UIBarButtonItem *trashButton;
@property (weak, nonatomic) IBOutlet UIBarButtonItem *actionButton;
@property (weak, nonatomic) IBOutlet UIImageView *imagePreview;
@property (weak, nonatomic) IBOutlet UISwitch *publicButton;
@property NSString *doAction;

- (IBAction)publicChanged:(id)sender;
- (IBAction)actionButtonClicked:(id)sender;
- (IBAction)deleteButtonClicked:(id)sender;
- (void)loadImage;

@property (strong, nonatomic) id detailItem;

@property (weak, nonatomic) IBOutlet UILabel *detailDescriptionLabel;
@end
