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

UITapGestureRecognizer *tap;
BOOL isFullScreen;
CGRect prevFrame;

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
        self.title = (NSString *)[self.detailItem objectForKey:@"name"];
        //self.detailDescriptionLabel.text = [self.detailItem description];

        NSString *status = [NSString stringWithFormat:@"%@",[self.detailItem objectForKey:@"is_public"]];

        if ([status isEqualToString:@"0"])
            [self.publicButton setOn:NO animated:NO];
        else
            [self.publicButton setOn:YES animated:NO];
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
        
        UITapGestureRecognizer *doubleTap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(handleDoubleTap:)];
        
        [doubleTap setNumberOfTapsRequired:2];
        [self.scrollView addGestureRecognizer:doubleTap];

        self.imagePreview.contentMode = UIViewContentModeScaleAspectFit;
    } else {
        [self alertStatus:[jsonData objectForKey:@"message"] :@"An error occured"];
    }
}

- (UIView *)viewForZoomingInScrollView:(UIScrollView *)scrollView
{
    return self.imagePreview;
}

-(void)handleDoubleTap:(id)sender
{
    [self.scrollView setZoomScale:1.0 animated:YES];
}


- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view, typically from a nib.
    [self configureView];

    NSArray *extensions = [NSArray arrayWithObjects: @"jpg", @"png", @"jpeg", @"JPG", @"PNG", @"JPEG", nil];
    if ([extensions containsObject:[[self.detailItem objectForKey:@"name"] pathExtension]])
    {
        self.scrollView.minimumZoomScale=1;
        self.scrollView.maximumZoomScale=6.0;
        self.scrollView.contentSize=CGSizeMake(self.view.bounds.size.width, self.view.bounds.size.height);
        self.scrollView.delegate=self;

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
    NSString *status = [NSString stringWithFormat:@"%@",[self.detailItem objectForKey:@"is_public"]];
    
    if ([status isEqualToString:@"0"]) {
        [self alertStatus:@"Your file is not public." :@"Error"];
    }
    else {
        NSString *string = [NSString stringWithFormat: @"J'aimerai partager avec toi mon fichier %@", [self.detailItem objectForKey:@"name"]];
        NSString *downloadUrl = [
            NSString stringWithFormat:@"http://cubbyhole.name/api/file/download/%@?accessKey=%@",
            [self.detailItem objectForKey:@"id"],
            [self.detailItem objectForKey:@"access_key"]
        ];
        NSURL *URL = [NSURL URLWithString:downloadUrl];
        
        UIActivityViewController* activityViewController = [[UIActivityViewController alloc] initWithActivityItems:@[string, URL] applicationActivities:nil];
        [self presentViewController:activityViewController animated:YES completion:^{}];
    }
}

- (IBAction)deleteButtonClicked:(id)sender {
    NSString *callUrl = [NSString stringWithFormat:@"http://cubbyhole.name/api/file/remove/%@", (NSString *)[self.detailItem objectForKey:@"id"]];
    NSURL *url = [NSURL URLWithString:callUrl];
    NSMutableURLRequest *request = [[NSMutableURLRequest alloc] init];

    [request setURL:url];
    [request setValue:@"5422e102a743fd70a22ee4ff7c2ebbe8" forHTTPHeaderField:@"X-API-KEY"];
    [request setHTTPMethod:@"DELETE"];
    [request setValue:@"application/json" forHTTPHeaderField:@"Accept"];
    [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
    
    NSError *error = [[NSError alloc] init];
    NSHTTPURLResponse *response = nil;
    NSData *urlData=[NSURLConnection sendSynchronousRequest:request returningResponse:&response error:&error];
    NSString *responseData = [[NSString alloc]initWithData:urlData encoding:NSUTF8StringEncoding];
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSDictionary *jsonData = (NSDictionary *) [jsonParser objectWithString:responseData error:nil];
    
    if ((long)[response statusCode] >=200 && (long)[response statusCode] <300)
    {
        NSInteger error = [(NSNumber *) [jsonData objectForKey:@"error"] integerValue];
        
        if(error == false)
        {
            NSDictionary *data = (NSDictionary *)[jsonData objectForKey:@"data"];

            [self alertStatus:[jsonData objectForKey:@"message"] :@"Success delete"];
            [self.navigationController popViewControllerAnimated:YES];
        } else {
            NSLog(@"%@", [jsonData objectForKey:@"message"]);
        }
    } else {
        NSLog(@"%@", [jsonData objectForKey:@"message"]);
    }
}

-(void) alertStatus:(NSString *)msg: (NSString *)title
{
    UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:title message:msg delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
    
    [alertView show];
}

- (IBAction)publicChanged:(id)sender {
        NSString *callUrl = [NSString stringWithFormat:@"http://cubbyhole.name/api/file/update/%@", (NSString *)[self.detailItem objectForKey:@"id"]];
        NSURL *url = [NSURL URLWithString:callUrl];
        NSMutableURLRequest *request = [[NSMutableURLRequest alloc] init];
        NSString *post =[[NSString alloc] initWithFormat:@"is_public=%d", ([self.publicButton isOn] ? 1 : 0)];
        NSData *postData = [post dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:YES];
        NSString *postLength = [NSString stringWithFormat:@"%lu", (unsigned long)[postData length]];
        
        [request setURL:url];
        [request setValue:@"5422e102a743fd70a22ee4ff7c2ebbe8" forHTTPHeaderField:@"X-API-KEY"];
        [request setHTTPMethod:@"POST"];
        [request setValue:@"application/json" forHTTPHeaderField:@"Accept"];
        [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
        [request setValue:postLength forHTTPHeaderField:@"Content-Length"];
        [request setHTTPBody:postData];
        
        NSError *error = [[NSError alloc] init];
        NSHTTPURLResponse *response = nil;
        NSData *urlData=[NSURLConnection sendSynchronousRequest:request returningResponse:&response error:&error];
        NSString *responseData = [[NSString alloc]initWithData:urlData encoding:NSUTF8StringEncoding];
        SBJsonParser *jsonParser = [SBJsonParser new];
        NSDictionary *jsonData = (NSDictionary *) [jsonParser objectWithString:responseData error:nil];
        
        if ((long)[response statusCode] >=200 && (long)[response statusCode] <300)
        {
            NSInteger error = [(NSNumber *) [jsonData objectForKey:@"error"] integerValue];
            
            if(error == false)
            {
                NSDictionary *data = (NSDictionary *)[jsonData objectForKey:@"data"];
                
                [self setDetailItem:(NSDictionary *)[data objectForKey:@"file"]];
                [self alertStatus:@"Your file is now public":@"Success"];
            } else {
                NSLog(@"Error: %@", [jsonData objectForKey:@"message"]);
            }
        } else {
            NSLog(@"Error response: %@", [jsonData objectForKey:@"message"]);
        }
}
@end
