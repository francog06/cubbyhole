//
//  MasterViewController.m
//  Cubbyhole
//
//  Created by Mathieu MORICEAU on 15/05/14.
//  Copyright (c) 2014 Cubbyhole Staff. All rights reserved.
//

#import "MasterViewController.h"

#import "DetailViewController.h"

@interface MasterViewController () {
    NSMutableArray *_objects;
}
@end

@implementation MasterViewController

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
        [self loadTable:self.folder_id];
}

- (void)awakeFromNib
{
    if ([[UIDevice currentDevice] userInterfaceIdiom] == UIUserInterfaceIdiomPad) {
        self.clearsSelectionOnViewWillAppear = NO;
        self.preferredContentSize = CGSizeMake(320.0, 600.0);
    }
    [super awakeFromNib];
}

-(void)loadTable:(NSString *)folder_id
{
    NSURL *url;
    NSString *callUrl;
    NSString *user_token = (NSString *)[[NSUserDefaults standardUserDefaults] objectForKey:@"user_token"];

    if (!_objects) {
        _objects = [[NSMutableArray alloc] init];
    }
    
    if ([_objects count]) {
        [_objects removeAllObjects];
        [self.tableView reloadData];
        [self loadTable:folder_id];
        return;
    }

    if (folder_id == nil || [folder_id isEqual:[NSNull null]]) {
        // Aucun folder définie, récupération du root
        NSString *user_id = (NSString *)[[NSUserDefaults standardUserDefaults] objectForKey:@"user_id"];

        self.navigationItem.leftBarButtonItems = [NSArray arrayWithObjects:nil];
        callUrl = [NSString stringWithFormat:@"http://cubbyhole.name/api/folder/user/%@/root", user_id];
        url = [NSURL URLWithString:callUrl];
    }
    else {
        // Folder définie récupération du folder
        self.navigationItem.leftBarButtonItems = [NSArray arrayWithObjects:self.backButton, nil];
        callUrl = [NSString stringWithFormat:@"http://cubbyhole.name/api/folder/details/%@", folder_id];
        url = [NSURL URLWithString:callUrl];
    }

    NSMutableURLRequest *request = [[NSMutableURLRequest alloc] init];
    [request setURL:url];
    [request setValue:user_token forHTTPHeaderField:@"X-API-KEY"];
    [request setHTTPMethod:@"GET"];
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
            data = (!folder_id) ? data : [data objectForKey:@"folder"];
            NSArray *files = (NSArray *)[data objectForKey:@"files"];
            
            self.current_folder = (!folder_id) ? nil : data;

            for (NSDictionary *file in files) {
                [file setValue:@"file" forKey:@"type"];
                [_objects insertObject:file atIndex:0];
                
                NSIndexPath *indexPath = [NSIndexPath indexPathForRow:0 inSection:0];
                [self.tableView insertRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationAutomatic];
            }
            
            NSArray *folders = (NSArray *)[data objectForKey:@"folders"];
            for (NSDictionary *folder in folders) {
                [folder setValue:@"folder" forKey:@"type"];
                [_objects insertObject:folder atIndex:0];
                
                NSIndexPath *indexPath = [NSIndexPath indexPathForRow:0 inSection:0];
                [self.tableView insertRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationAutomatic];
            }

            NSString *folderName = (NSString *)[self.current_folder objectForKey:@"name"];
            if (folderName == nil || [folderName isEqual:[NSNull null]])
                self.title = @"Root folder";
            else
                self.title = folderName;
            [SVProgressHUD dismiss];
        } else {
            [SVProgressHUD dismiss];
            NSLog(@"%@", [jsonData objectForKey:@"message"]);
        }
    } else {
        [SVProgressHUD dismiss];
        NSLog(@"%@", [jsonData objectForKey:@"message"]);
    }
}

-(void)Back
{
    NSString *parent = (NSString *)[self.current_folder objectForKey:@"parent"];

    if (parent == nil || [parent isEqual:[NSNull null]]) {
        self.folder_id = nil;
        [SVProgressHUD show];
    }
    else {
        self.folder_id = parent;
        [SVProgressHUD show];
    }
}

- (void)refreshTable
{
    [self loadTable:self.folder_id];
    [self performSelector:@selector(stopRefresh) withObject:nil afterDelay:2.5];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    self.backButton = [[UIBarButtonItem alloc] initWithTitle:@"Back" style:UIBarButtonItemStyleBordered target:self action:@selector(Back)];

    self.navigationItem.hidesBackButton = YES;
    self.navigationItem.leftBarButtonItems = [NSArray arrayWithObjects:self.backButton, self.editButtonItem, nil];
    self.folder_id = nil;

    UIBarButtonItem *addButton = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemAdd target:self action:@selector(insertNewObject:)];
    UIBarButtonItem *disconnectButton = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemReply target:self action:@selector(disconnectUser:)];
    self.navigationItem.rightBarButtonItems = [NSArray arrayWithObjects:disconnectButton, addButton, nil];
    self.detailViewController = (DetailViewController *)[[self.splitViewController.viewControllers lastObject] topViewController];
    
    /* Pull to refresh */
    
    UIRefreshControl *refresh = [[UIRefreshControl alloc] init];
    
    refresh.attributedTitle = [[NSAttributedString alloc] initWithString:@"Pull to Refresh"];
    [refresh addTarget:self action:@selector(refreshTable) forControlEvents:UIControlEventValueChanged];
    self.refreshControl = refresh;

    [self loadTable:nil];
}

- (void)stopRefresh
{
    [self.refreshControl endRefreshing];
    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
}

- (void)disconnectUser:(id)sender
{
    NSUserDefaults * defs = [NSUserDefaults standardUserDefaults];
    NSDictionary * dict = [defs dictionaryRepresentation];
    for (id key in dict) {
        [defs removeObjectForKey:key];
    }
    [defs synchronize];
    [self alertStatus:@"Successfull logout" :@"Success"];
    [self.navigationController popToRootViewControllerAnimated:YES];
}

- (void)insertNewObject:(id)sender
{
    /*
    if (!_objects) {
        _objects = [[NSMutableArray alloc] init];
    }
    [_objects insertObject:[NSDate date] atIndex:0];
    NSIndexPath *indexPath = [NSIndexPath indexPathForRow:0 inSection:0];
    [self.tableView insertRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationAutomatic];
     */
}

#pragma mark - Table View

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return _objects.count;
}

- (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath
{
    // update your model
}

// Override to support conditional rearranging of the table view.
- (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the item to be re-orderable.
    return YES;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"Cell" forIndexPath:indexPath];

    NSDictionary *object = _objects[indexPath.row];
    NSDictionary *last_update_path = [object objectForKey:@"last_update_date"];

    cell = [cell initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:@"Cell"];
    
    if ( [(NSString *)[object objectForKey:@"type"] isEqualToString:@"folder"] ) {
        cell.textLabel.textColor = [UIColor darkGrayColor];
        cell.imageView.image = [UIImage imageNamed:@"folder.png"];
    }
    else {
        cell.imageView.image = [UIImage imageNamed:@"file.png"];
        cell.textLabel.textColor = [UIColor colorWithRed:(30/255.0) green:(114/255.0) blue:(241/255.0) alpha:1];
    }

    cell.textLabel.text = [object objectForKey:@"name"];
    cell.detailTextLabel.text = [last_update_path objectForKey:@"date"];
    cell.detailTextLabel.textColor = [UIColor lightGrayColor];
    cell.detailTextLabel.numberOfLines = 2;
    return cell;
}

- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
{
    return YES;
}

- (void)deleteEntity:(NSIndexPath *)indexPath
{
    NSString *callUrl;
    NSDictionary *object = _objects[indexPath.row];

        callUrl = [NSString stringWithFormat:@"http://cubbyhole.name/api/%@/remove/%@",
                   (NSString *)[object  objectForKey:@"type"],
                   (NSString *)[object  objectForKey:@"id"]];
    NSURL *url = [NSURL URLWithString:callUrl];
    NSMutableURLRequest *request = [[NSMutableURLRequest alloc] init];
    NSString *token = (NSString *)[[NSUserDefaults standardUserDefaults] objectForKey:@"user_token"];

    [request setURL:url];
    [request setValue:token forHTTPHeaderField:@"X-API-KEY"];
    [request setHTTPMethod:@"DELETE"];
    [request setValue:@"application/json" forHTTPHeaderField:@"Accept"];
    [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
    
    NSLog(@"Delete");
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
            [self alertStatus:[jsonData objectForKey:@"message"] :@"Success delete"];
            [_objects removeObjectAtIndex:indexPath.row];
            [self.tableView deleteRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationFade];
        } else {
            NSLog(@"%@", [jsonData objectForKey:@"message"]);
        }
    } else {
        NSLog(@"%@", [jsonData objectForKey:@"message"]);
    }
}

- (void)startEditing {
    [self setEditing:YES animated:YES];
}

- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (editingStyle == UITableViewCellEditingStyleDelete) {
        [self deleteEntity:indexPath];
    } else if (editingStyle == UITableViewCellEditingStyleInsert) {
        NSLog(@"Editing style");
    } else {
        // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view.
        NSLog(@"Unhandled editing style! %ld", editingStyle);
    }
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    if ([[UIDevice currentDevice] userInterfaceIdiom] == UIUserInterfaceIdiomPad) {
        NSDate *object = _objects[indexPath.row];
        self.detailViewController.detailItem = object;
    }
}

- (BOOL)shouldPerformSegueWithIdentifier:(NSString *)identifier sender:(id)sender
{
    if ([identifier isEqualToString:@"showDetail"]) {
        NSIndexPath *indexPath = [self.tableView indexPathForSelectedRow];
        NSDictionary *object = _objects[indexPath.row];
        NSString *type = [object objectForKey:@"type"];

        if ([type isEqualToString:@"folder"]) {
            NSString *folder_id = (NSString *)[object objectForKey:@"id"];
            
            self.folder_id = folder_id;
            [SVProgressHUD show];
            return NO;
        }
        else
            return YES;
    }
    return NO;
}

- (void)setDetailItem:(id)newDetailItem
{
    if (newDetailItem == nil) {
        NSLog(@"Root");
    }
}

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    if ([[segue identifier] isEqualToString:@"showDetail"]) {
        NSIndexPath *indexPath = [self.tableView indexPathForSelectedRow];
        [[segue destinationViewController] setDetailItem:_objects[indexPath.row]];
    }
}

-(void) alertStatus:(NSString *)msg: (NSString *)title
{
    UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:title message:msg delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
    
    [alertView show];
}

@end
