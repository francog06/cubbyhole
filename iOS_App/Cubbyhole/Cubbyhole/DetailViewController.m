//
//  DetailViewController.m
//  Cubbyhole
//
//  Created by Mathieu MORICEAU on 15/05/14.
//  Copyright (c) 2014 Cubbyhole Staff. All rights reserved.
//

#import "DetailViewController.h"

@interface DetailViewController ()
@property (strong, nonatomic) UIPopoverController *masterPopoverController;
- (void)configureView;
@end

@implementation DetailViewController

- (void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(handleNotification:)
                                                 name:SVProgressHUDDidAppearNotification
                                               object:nil];
}

- (void)handleNotification:(NSNotification *)notif
{
    if ([notif.name isEqualToString:SVProgressHUDDidAppearNotification])
        [self loadImage];
}

#pragma mark - Managing the detail item

- (void)setDetailItem:(id)newDetailItem
{
    if (_detailItem != newDetailItem) {
        _detailItem = newDetailItem;

        // Update the view.
        [self configureView];
    }

    if (self.masterPopoverController != nil) {
        [self.masterPopoverController dismissPopoverAnimated:YES];
    }        
}

- (void)configureView
{
    // Update the user interface for the detail item.

    if (self.detailItem) {
        //self.detailDescriptionLabel.text = [self.detailItem description];
    }
}

- (void)loadImage
{
    NSString *specialKey = @"ab14d0415c485464a187d5a9c97cc27c";
    [SVProgressHUD dismiss];
    NSString *folder_ID = (NSString *)[self.detailItem objectForKey:@"id"];
    NSString *callUrl = [NSString stringWithFormat:@"http://cubbyhole.name/api/file/details/%@/preview?hash=%@", folder_ID, specialKey];

    NSURL *url = [NSURL URLWithString:callUrl];
    NSMutableURLRequest *request = [[NSMutableURLRequest alloc] init];
    NSError *error = [[NSError alloc] init];
    NSHTTPURLResponse *response = nil;
    
    NSString *token = (NSString *)[[NSUserDefaults standardUserDefaults] objectForKey:@"userToken"];

    [request setURL:url];
    [request setHTTPMethod:@"GET"];
    [request setValue:@"5422e102a743fd70a22ee4ff7c2ebbe8" forHTTPHeaderField:@"X-API-KEY"];
        
    NSData *data=[NSURLConnection sendSynchronousRequest:request returningResponse:&response error:&error];
    NSString *responseData = [[NSString alloc]initWithData:data encoding:NSUTF8StringEncoding];
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSDictionary *jsonData = (NSDictionary *) [jsonParser objectWithString:responseData error:nil];
    UIImage *img = [[UIImage alloc]initWithData:data ];

    [SVProgressHUD dismiss];
    if ((long)[response statusCode] >=200 && (long)[response statusCode] <300)
    {
        [self.imagePreview initWithImage:img].hidden = NO;
        self.imagePreview.contentMode = UIViewContentModeScaleAspectFit;
    } else {
        [self alertStatus:[jsonData objectForKey:@"message"] :@"An error occured"];
    }
}

- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view, typically from a nib.
    [self configureView];

    NSArray *extensions = [NSArray arrayWithObjects: @"jpg", @"png", @"jpeg", @"JPG", @"PNG", @"JPEG", nil];
    if ([extensions containsObject:[[self.detailItem objectForKey:@"name"] pathExtension]])
    {
        [SVProgressHUD show];
    }
    else
    {
        self.detailDescriptionLabel.text = @"No preview available for this file.";
    }
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Split view

- (void)splitViewController:(UISplitViewController *)splitController willHideViewController:(UIViewController *)viewController withBarButtonItem:(UIBarButtonItem *)barButtonItem forPopoverController:(UIPopoverController *)popoverController
{
    barButtonItem.title = NSLocalizedString(@"Master", @"Master");
    [self.navigationItem setLeftBarButtonItem:barButtonItem animated:YES];
    self.masterPopoverController = popoverController;
}

- (void)splitViewController:(UISplitViewController *)splitController willShowViewController:(UIViewController *)viewController invalidatingBarButtonItem:(UIBarButtonItem *)barButtonItem
{
    // Called when the view is shown again in the split view, invalidating the button and popover controller.
    [self.navigationItem setLeftBarButtonItem:nil animated:YES];
    self.masterPopoverController = nil;
}

- (IBAction)actionButtonClicked:(id)sender {
    NSLog(@"Action object: %@", self.detailItem);
}

- (IBAction)deleteButtonClicked:(id)sender {
}

-(void) alertStatus:(NSString *)msg: (NSString *)title
{
    UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:title message:msg delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
    
    [alertView show];
}
@end
