<!--pages/forum/forum.wxml-->
<view class="container">
    <view class="page-body">
        <view class="forum-list">
            <block wx:for-items="{{group_list}}" wx:key="">
                <view id="{{item.fid}}" class="forum-cells-group" bindtap='clickGroup'>
                    <view>{{item.name}}</view>
                    <image class="{{item.open ? 'arrow-trans' : ''}}" src='/resources/image/arrowright.png'></image>
                </view>
                <view class="forum-cells {{item.open ? 'forum-list_show' : 'forum-list_hide'}}">
                    <block wx:for-items="{{item.sub_group}}" wx:for-item="sub_group" wx:key="">
                        <view class="forum-cell" bindtap="toForumList" data-fid="{{sub_group.fid}}">
                                <view class="sub_group_title">{{sub_group.name}}</view>
                                <view class="sub_group_note"> 
                                    主题: {{sub_group.threads}} 帖子: {{sub_group.posts}}
                                </view>
                        </view>
                    </block>
                </view>
            </block>
        </view>
  </view>
</view>