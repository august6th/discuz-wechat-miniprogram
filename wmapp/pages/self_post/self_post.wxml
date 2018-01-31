<!--pages/self_post/self_post.wxml-->
<!--index.wxml-->
<import src="/wxParse/wxParse.wxml"/>
<view class="container">
  <view class="page-body">
    <view class="page__bd">
      <view>
      	<block wx:for="{{postList}}" wx:for-index="idx" wx:for-item="post"  wx:key="">
      		<view class="post_cell" bindtap="toDetail" data-tid="{{post.tid}}">
            <view class="post_message">
              <block wx:for="{{replyTemArray}}" wx:key="">
                <block wx:if="{{idx == index}}">
                <template is="wxParse" data="{{wxParseData:item}}"/>
                </block>
              </block>
            </view>
      			 <!-- <view class="post_message">{{post.message}}</view>  -->
      			<view class="post_thread">帖子：{{post.thread_subject}}</view>
      			<view class="post_create_time">{{post.create_time}}</view>
      		</view>
      	</block>
      </view>
    </view>
  </view>
</view>