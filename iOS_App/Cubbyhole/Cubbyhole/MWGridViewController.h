//
//  MWGridViewController.h
//  MWPhotoBrowser
//
//  Created by Michael Waterfall on 08/10/2013.
//
//

#import "MWPhotoBrowser.h"
#import <UIKit/UIKit.h>
#import "PSTCollectionView/PSTCollectionViewController.h"

@interface MWGridViewController : PSTCollectionViewController {}

@property (nonatomic, assign) MWPhotoBrowser *browser;
@property (nonatomic) BOOL selectionMode;
@property (nonatomic) CGPoint initialContentOffset;

@end
