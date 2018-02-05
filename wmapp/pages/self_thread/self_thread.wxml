<!--index.wxml-->
<view class="container">
  <view class="page-body">
    <view class="page__bd">
        <block wx:for="{{articleList}}" wx:for-item="article" wx:key="">
          <view class="article" bindtap="toDetail" data-tid="{{article.tid}}">
            <view class="article-content">
              <view class="article-header">
                <!-- <view class="article-author-avatar">
                  <image src='{{article.avatar}}'></image>
                </view> -->
                <view class="article-side-header">
                  <view class="article-sub-header">
                    <view class="article-title">{{article.subject}}</view>
                  </view>
                  <view class="article-info">
                     <view class="article-author">{{article.author}}</view>    
                    <view class="article-in">in</view>
                    <view class="article-fid-item">{{article.fid_name}}</view>
                  </view>
                </view>
              </view>
              <block wx:if="{{!lite_switch}}">
                <block wx:if="{{article.image_list.length > 0}}">
                 <view  class="article-img">
                    <view class="article-img-info">
                      <image class="article-img-item" src='{{article.image_list[0]}}'></image>
                    </view>
                  </view> 
                </block>
                <block wx:if="{{article.message}}">
                  <view class="article-message">
                    {{article.message}}
                  </view>
                </block>
              </block>
            <view class="article-ext-info">
              <view class="article-re">
                <view class="zan-icon zan-icon-password-view"></view> 
                <view>{{article.views}}</view>
                <view class="zan-icon zan-icon-chat"></view>
                <view>{{article.replies}}</view>
              </view>
              <view class="article-post-time">{{article.create_time}}</view>              
            </view>
          </view>
          </view>
        </block>
    </view>
  </view> 
</view>