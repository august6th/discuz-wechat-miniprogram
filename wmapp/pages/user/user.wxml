<!--user.wxml-->
<view class="container">

  <loading hidden="{{loading_hidden}}">
        {{loading_msg}}
  </loading>

  <view class="page-head-info">
      <image class="userinfo-avatar" src="{{userInfo.avatarUrl}}"></image>
      <view class="userinfo-nickname">{{userInfo.nickName}}</view>
  </view>

  <view class="page-body">
    <view class="user-list">
      <block wx:if="{{hasUserInfo === false}}">
        <!--
        <view class="navigator" bindtap="wxlogin">
          <view class="navigator-text">微信登录</view>
          <view class="navigator-arrow"></view>
        </view>
        -->
        <navigator url="../login/login" class="navigator">
          <view class="navigator-text">账号登录</view>
          <view class="navigator-arrow"></view>
        </navigator>
        <!--
        <navigator url="../register/register" class="navigator">
          <view class="navigator-text">账号注册</view>
          <view class="navigator-arrow"></view>
        </navigator>
        -->
      </block>
      
      <block wx:if="{{hasUserInfo === true}}">
        <navigator url="../self_thread/self_thread" class="navigator">
          <view class="navigator-text">我的帖子</view>
          <view class="navigator-arrow"></view>
        </navigator>
        <navigator url="../self_post/self_post" class="navigator">
          <view class="navigator-text">我的回复</view>
          <view class="navigator-arrow"></view>
        </navigator>
      </block>
    </view>

    <block wx:if="{{hasUserInfo === true}}">
      <view class="btn-area">
        <button class="weui-btn" type="warn" bindtap="logout">退出登录</button>
      </view>
    </block>
  </view>
</view>