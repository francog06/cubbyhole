//
//  LoginViewController.m
//  Cubbyhole
//
//  Created by Mathieu MORICEAU on 15/05/14.
//  Copyright (c) 2014 Cubbyhole Staff. All rights reserved.
//

#import "LoginViewController.h"

@interface LoginViewController ()

@end

@implementation LoginViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(handleNotification:)
                                                 name:SVProgressHUDDidAppearNotification
                                               object:nil];
}

- (void)viewWillDisappear:(BOOL)animated
{
    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

- (void)handleNotification:(NSNotification *)notif
{
    if ([notif.name isEqualToString:SVProgressHUDDidAppearNotification])
        [self logUser];
}

-(void)registerUser
{
    @try {
        if ( [[[self txtEmail] text] isEqualToString:@""] || [[[self txtPassword] text] isEqualToString:@""]) {
            [self alertStatus:@"Please enter both Username and Password" :@"Login Failed!"];
        } else {
            NSString *post =[[NSString alloc] initWithFormat:@"email=%@&password=%@",[[self txtEmail] text],[[self txtPassword] text]];
            NSLog(@"PostData: %@",post);
            
            NSURL *url=[NSURL URLWithString:@"http://cubbyhole.name/api/user/register"];
            
            NSData *postData = [post dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:YES];
            
            NSString *postLength = [NSString stringWithFormat:@"%lu", (unsigned long)[postData length]];
            
            NSMutableURLRequest *request = [[NSMutableURLRequest alloc] init];
            [request setURL:url];
            [request setHTTPMethod:@"POST"];
            [request setValue:postLength forHTTPHeaderField:@"Content-Length"];
            [request setValue:@"application/json" forHTTPHeaderField:@"Accept"];
            [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
            [request setHTTPBody:postData];
            
            NSError *error = [[NSError alloc] init];
            NSHTTPURLResponse *response = nil;
            NSData *urlData=[NSURLConnection sendSynchronousRequest:request returningResponse:&response error:&error];
            NSString *responseData = [[NSString alloc]initWithData:urlData encoding:NSUTF8StringEncoding];
            SBJsonParser *jsonParser = [SBJsonParser new];
            NSDictionary *jsonData = (NSDictionary *) [jsonParser objectWithString:responseData error:nil];
            
            [SVProgressHUD dismiss];
            if ((long)[response statusCode] >=200 && (long)[response statusCode] <300)
            {
                NSInteger success = [(NSNumber *) [jsonData objectForKey:@"error"] integerValue];
                
                if(success == false)
                {
                    [self performSegueWithIdentifier:@"AfterLogin" sender:self];
                } else {
                    [self alertStatus:[jsonData objectForKey:@"message"] :@"Register Failed"];
                }
            } else {
                [self alertStatus:[jsonData objectForKey:@"message"] :@"Register Failed"];
            }
        }
    }
    @catch (NSException * e) {
        NSLog(@"Exception: %@", e);
        [self alertStatus:@"Register failed" :@"Register Failed"];
    }
}

- (void)logUser
{
    @try {
        if ( [[[self txtEmail] text] isEqualToString:@""] || [[[self txtPassword] text] isEqualToString:@""]) {
            [self alertStatus:@"Please enter both Username and Password" :@"Login Failed!"];
        } else {
            NSString *post =[[NSString alloc] initWithFormat:@"email=%@&password=%@",[[self txtEmail] text],[[self txtPassword] text]];
            NSLog(@"PostData: %@",post);
            
            NSURL *url=[NSURL URLWithString:@"http://cubbyhole.name/api/user/login"];
            
            NSData *postData = [post dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:YES];
            
            NSString *postLength = [NSString stringWithFormat:@"%lu", (unsigned long)[postData length]];
            
            NSMutableURLRequest *request = [[NSMutableURLRequest alloc] init];
            [request setURL:url];
            [request setHTTPMethod:@"POST"];
            [request setValue:postLength forHTTPHeaderField:@"Content-Length"];
            [request setValue:@"application/json" forHTTPHeaderField:@"Accept"];
            [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
            [request setHTTPBody:postData];
            
            NSError *error = [[NSError alloc] init];
            NSHTTPURLResponse *response = nil;
            NSData *urlData=[NSURLConnection sendSynchronousRequest:request returningResponse:&response error:&error];
            NSString *responseData = [[NSString alloc]initWithData:urlData encoding:NSUTF8StringEncoding];
            SBJsonParser *jsonParser = [SBJsonParser new];
            NSDictionary *jsonData = (NSDictionary *) [jsonParser objectWithString:responseData error:nil];
            
            [SVProgressHUD dismiss];
            if ((long)[response statusCode] >=200 && (long)[response statusCode] <300)
            {
                NSInteger success = [(NSNumber *) [jsonData objectForKey:@"error"] integerValue];
                
                if(success == false)
                {
                    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
                    NSDictionary *data = (NSDictionary *)[jsonData objectForKey:@"data"];
                    
                    [defaults setObject:[data objectForKey:@"token"] forKey:@"userToken"];
                    [defaults setObject:[data objectForKey:@"user"] forKey:@"user"];
                    [defaults synchronize];
                    
                    [self performSegueWithIdentifier:@"AfterLogin" sender:self];
                } else {
                    [self alertStatus:[jsonData objectForKey:@"message"] :@"Login Failed"];
                }
            } else {
                [self alertStatus:[jsonData objectForKey:@"message"] :@"Login Failed"];
            }
        }
    }
    @catch (NSException * e) {
        NSLog(@"Exception: %@", e);
        [self alertStatus:@"Login Failed" :@"Login Failed"];
    }
}

- (void)viewDidLoad
{
    [super viewDidLoad];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
}

- (IBAction)loginClicked:(id)sender {
    [SVProgressHUD show];
}

- (IBAction)registerClicked:(id)sender {
    [self registerUser];
}

- (IBAction)backgroundClick:(id)sender {
    [[self txtEmail] resignFirstResponder];
    [[self txtPassword] resignFirstResponder];
}

-(void) alertStatus:(NSString *)msg: (NSString *)title
{
    UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:title message:msg delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
    
    [alertView show];
}

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    if ([[segue identifier] isEqualToString:@"showDetail"]) {
        [segue destinationViewController];
    }
}
@end
